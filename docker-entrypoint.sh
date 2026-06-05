#!/bin/bash
set -e

echo "Starting entrypoint script..."

# --- Map Railway MySQL variables to your app's expected names ---
export DB_HOST=${MYSQLHOST}
export DB_PORT=${MYSQLPORT}
export DB_USER=${MYSQLUSER}
export DB_PASSWORD=${MYSQLPASSWORD}
export DB_NAME=${MYSQLDATABASE}
export APP_ENV=${APP_ENV:-production}

# Debug: print mapped variables (hide password)
echo "Database configuration from Railway:"
echo "  MYSQLHOST: ${MYSQLHOST}"
echo "  DB_HOST:   ${DB_HOST}"
echo "  MYSQLPORT: ${MYSQLPORT}"
echo "  DB_PORT:   ${DB_PORT}"
echo "  MYSQLDATABASE: ${MYSQLDATABASE}"
echo "  DB_NAME:   ${DB_NAME}"
echo "  DB_USER:   ${DB_USER}"
# Do not print DB_PASSWORD

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

# --- Run database migrations (using PHP) ---
if [ -d "/var/www/database" ] && [ -n "$DB_HOST" ] && [ -n "$DB_NAME" ]; then
    echo "Running database migrations..."
    # Create a tiny PHP script to run migrations
    cat > /tmp/run_migrations.php <<'EOPHP'
<?php
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASSWORD');

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $migrationDir = __DIR__ . '/../database';
    $files = glob($migrationDir . '/*.sql');
    if ($files) {
        sort($files);
        foreach ($files as $file) {
            echo "Executing: " . basename($file) . "\n";
            $sql = file_get_contents($file);
            $pdo->exec($sql);
        }
        echo "Migrations completed.\n";
    } else {
        echo "No SQL files found in database/.\n";
    }
} catch (Exception $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
    // Do not stop Apache if migrations fail – maybe tables already exist
}
EOPHP
    php /tmp/run_migrations.php
fi

# --- Fix permissions (your requested chown/chmod) ---
# Note: /var/www is the working directory; your files are already there.
chown -R www-data:www-data /var/www
chmod -R 755 /var/www
# Ensure the database folder (if any) is writable for future migrations
if [ -d "/var/www/database" ]; then
    chown -R www-data:www-data /var/www/database
    chmod -R 775 /var/www/database
fi

# --- Start Apache ---
echo "Starting Apache on port ${PORT}..."
exec apache2-foreground