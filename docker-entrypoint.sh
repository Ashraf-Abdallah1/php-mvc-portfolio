#!/bin/bash
set -e

echo "Starting entrypoint script..."

# Print DB_* values for debugging
echo "DB_HOST = ${DB_HOST}"
echo "DB_PORT = ${DB_PORT}"
echo "DB_NAME = ${DB_NAME}"
echo "DB_USER = ${DB_USER}"

# Test connection
php -r "
\$host = getenv('DB_HOST');
\$port = getenv('DB_PORT');
\$db   = getenv('DB_NAME');
\$user = getenv('DB_USER');
\$pass = getenv('DB_PASSWORD');
if (empty(\$host)) {
    fwrite(STDERR, 'ERROR: DB_HOST not set\n');
    exit(1);
}
try {
    new PDO(\"mysql:host=\$host;port=\$port;dbname=\$db;charset=utf8mb4\", \$user, \$pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo \"DB connection OK\n\";
} catch (Exception \$e) {
    fwrite(STDERR, 'DB error: ' . \$e->getMessage() . \"\n\");
    exit(1);
}
" || exit 1

# Apache config (MPM, rewrite, port)
a2dismod mpm_event 2>/dev/null || true
a2dismod mpm_worker 2>/dev/null || true
a2dismod mpm_prefork 2>/dev/null || true
a2enmod mpm_prefork
a2enmod rewrite

PORT=${PORT:-8080}
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
echo "ServerName localhost:${PORT}" >> /etc/apache2/apache2.conf

DOCROOT="/var/www/public"
cat > /etc/apache2/sites-available/000-default.conf <<EOF
<VirtualHost *:${PORT}>
    DocumentRoot ${DOCROOT}
    <Directory ${DOCROOT}>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOF

chown -R www-data:www-data /var/www
chmod -R 755 /var/www

echo "Starting Apache on port ${PORT}..."
exec apache2-foreground