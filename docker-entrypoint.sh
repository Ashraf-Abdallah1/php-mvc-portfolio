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

# --- YOUR SUGGESTED FIXES (applied at runtime) ---
echo "Configuring Apache directives..."
sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

# Determine the correct DocumentRoot
# If your project has a 'public' folder inside /var/www (from Dockerfile COPY), use that.
# If your project's index.php is in the root, adjust accordingly.
DOCROOT="/var/www/public"

# If you prefer /var/www/html/public (as you wrote), uncomment next line:
# DOCROOT="/var/www/html/public"

sed -i "s|/var/www/html|${DOCROOT}|g" /etc/apache2/sites-available/000-default.conf

# --- Ensure the directory exists and has content ---
mkdir -p "${DOCROOT}"
if [ ! -f "${DOCROOT}/index.php" ] && [ ! -f "${DOCROOT}/index.html" ]; then
    echo "Creating default index.php for testing"
    echo "<?php phpinfo(); ?>" > "${DOCROOT}/index.php"
fi

# --- Fix ownership and permissions ---
chown -R www-data:www-data "${DOCROOT}"
chmod -R 755 "${DOCROOT}"

# --- Explicitly allow access in Apache config ---
cat > /etc/apache2/conf-available/docroot-permissions.conf <<EOF
<Directory ${DOCROOT}>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
EOF
a2enconf docroot-permissions

# --- Start Apache ---
echo "Starting Apache on port ${PORT}..."
exec apache2-foreground