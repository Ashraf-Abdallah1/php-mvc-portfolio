#!/bin/bash
set -e

echo "Starting entrypoint script..."

# --- Fix for Apache MPM conflicts ---
a2dismod mpm_event 2>/dev/null || true
a2dismod mpm_worker 2>/dev/null || true
a2dismod mpm_prefork 2>/dev/null || true
a2enmod mpm_prefork

# --- Set dynamic port for Railway ---
PORT=${PORT:-8080}
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
echo "ServerName localhost:${PORT}" >> /etc/apache2/apache2.conf

# --- FORCE DocumentRoot to /var/www/public ---
DOCROOT="/var/www/public"
# Override the default site configuration completely
cat > /etc/apache2/sites-available/000-default.conf <<EOF
<VirtualHost *:${PORT}>
    ServerAdmin webmaster@localhost
    DocumentRoot ${DOCROOT}
    <Directory ${DOCROOT}>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF

# Also set the default Apache2.conf to allow overrides globally
sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# --- Ensure the directory exists and has content ---
mkdir -p "${DOCROOT}"
if [ ! -f "${DOCROOT}/index.php" ] && [ ! -f "${DOCROOT}/index.html" ]; then
    echo "<?php phpinfo(); ?>" > "${DOCROOT}/index.php"
fi

# --- Fix permissions ---
chown -R www-data:www-data "${DOCROOT}"
chmod -R 755 "${DOCROOT}"

# --- Start Apache ---
echo "Starting Apache on port ${PORT}..."
exec apache2-foreground