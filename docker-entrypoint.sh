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

# --- Configure DocumentRoot and permissions ---
DOCROOT="/var/www/public"

# Create default content if directory is empty (for testing)
if [ ! -f "$DOCROOT/index.php" ] && [ ! -f "$DOCROOT/index.html" ]; then
    echo "WARNING: No index file found in $DOCROOT. Creating a default phpinfo page."
    mkdir -p "$DOCROOT"
    echo "<?php phpinfo(); ?>" > "$DOCROOT/index.php"
fi

# Ensure DocumentRoot is set correctly (in case build-time sed failed)
sed -i "s#/var/www/html#$DOCROOT#" /etc/apache2/sites-available/000-default.conf

# Add explicit directory permissions for the new DocumentRoot
cat > /etc/apache2/conf-available/docroot-permissions.conf <<EOF
<Directory $DOCROOT>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
EOF
a2enconf docroot-permissions

# Fix filesystem ownership and permissions
chown -R www-data:www-data "$DOCROOT"
chmod -R 755 "$DOCROOT"

# --- Start Apache ---
echo "Starting Apache on port ${PORT}..."
exec apache2-foreground