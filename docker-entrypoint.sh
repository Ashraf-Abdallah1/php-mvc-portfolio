#!/bin/bash
set -e

echo "Starting entrypoint script..."

# --- Print ALL Railway environment variables (for debugging) ---
echo "=== All MYSQL* variables from Railway ==="
env | grep MYSQL || echo "No MYSQL variables found!"
echo "=========================================="

# --- Map Railway MySQL variables to DB_* ---
export DB_HOST=${MYSQLHOST}
export DB_PORT=${MYSQLPORT}
export DB_USER=${MYSQLUSER}
export DB_PASSWORD=${MYSQLPASSWORD}
export DB_NAME=${MYSQLDATABASE}
export APP_ENV=${APP_ENV:-production}

echo "Mapped DB_* variables:"
echo "  DB_HOST = '${DB_HOST}'"
echo "  DB_PORT = '${DB_PORT}'"
echo "  DB_NAME = '${DB_NAME}'"
echo "  DB_USER = '${DB_USER}'"

# --- Test database connection using PHP ---
echo "Testing database connection..."
php -r "
\$host = getenv('DB_HOST');
\$port = getenv('DB_PORT');
\$db   = getenv('DB_NAME');
\$user = getenv('DB_USER');
\$pass = getenv('DB_PASSWORD');
if (empty(\$host)) {
    fwrite(STDERR, 'ERROR: DB_HOST is empty!\\n');
    exit(1);
}
try {
    \$pdo = new PDO(\"mysql:host=\$host;port=\$port;dbname=\$db;charset=utf8mb4\", \$user, \$pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo \"Connection successful!\\n\";
} catch (Exception \$e) {
    fwrite(STDERR, \"Connection failed: \" . \$e->getMessage() . \"\\n\");
    exit(1);
}
" || {
    echo "FATAL: Could not connect to database. Aborting."
    exit 1
}

# --- Fix for Apache MPM conflicts ---
a2dismod mpm_event 2>/dev/null || true
a2dismod mpm_worker 2>/dev/null || true
a2dismod mpm_prefork 2>/dev/null || true
a2enmod mpm_prefork

# --- Enable mod_rewrite for .htaccess ---
a2enmod rewrite

# --- Set dynamic port for Railway ---
PORT=${PORT:-8080}
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
echo "ServerName localhost:${PORT}" >> /etc/apache2/apache2.conf

# --- Configure DocumentRoot and allow .htaccess ---
DOCROOT="/var/www/public"
cat > /etc/apache2/sites-available/000-default.conf <<EOF
<VirtualHost *:${PORT}>
    DocumentRoot ${DOCROOT}
    <Directory ${DOCROOT}>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog /var/log/apache2/error.log
    CustomLog /var/log/apache2/access.log combined
</VirtualHost>
EOF

# --- Fix permissions ---
chown -R www-data:www-data /var/www
chmod -R 755 /var/www
if [ -d "/var/www/database" ]; then
    chown -R www-data:www-data /var/www/database
    chmod -R 775 /var/www/database
fi

# --- Start Apache ---
echo "Starting Apache on port ${PORT}..."
exec apache2-foreground