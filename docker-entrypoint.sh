#!/bin/bash
set -e

echo "Starting entrypoint script..."

# --- Fix for Apache MPM conflicts ---
echo "Disabling conflicting MPM modules..."
a2dismod mpm_event 2>/dev/null || true
a2dismod mpm_worker 2>/dev/null || true
a2dismod mpm_prefork 2>/dev/null || true

echo "Enabling mpm_prefork module..."
a2enmod mpm_prefork

# --- Set dynamic port for Railway ---
PORT=${PORT:-8080}
echo "Setting Apache to listen on port ${PORT}..."
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
echo "ServerName localhost:${PORT}" >> /etc/apache2/apache2.conf

# --- Ensure correct permissions for DocumentRoot ---
echo "Fixing permissions for /var/www/public..."
if [ -d "/var/www/public" ]; then
    # Change ownership to www-data (Apache user)
    chown -R www-data:www-data /var/www/public
    # Ensure directories have execute permission
    chmod -R 755 /var/www/public
else
    echo "WARNING: /var/www/public does not exist!"
    # Create a default index.php to test
    mkdir -p /var/www/public
    echo "<?php phpinfo(); ?>" > /var/www/public/index.php
    chown -R www-data:www-data /var/www/public
fi

# --- Start Apache ---
echo "Starting Apache on port ${PORT}..."
exec apache2-foreground