#!/bin/bash
set -e

echo "Starting entrypoint script..."

# --- Fix for Apache MPM conflicts ---
a2dismod mpm_event 2>/dev/null || true
a2dismod mpm_worker 2>/dev/null || true
a2dismod mpm_prefork 2>/dev/null || true
a2enmod mpm_prefork

# --- Enable mod_rewrite (critical for your .htaccess) ---
a2enmod rewrite

# --- Set dynamic port for Railway ---
PORT=${PORT:-8080}
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
echo "ServerName localhost:${PORT}" >> /etc/apache2/apache2.conf

# --- Override DocumentRoot and allow .htaccess overrides ---
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

# --- Ensure your original .htaccess is preserved (if it exists) ---
# No changes to .htaccess needed – just make sure mod_rewrite is on.

# --- Fix permissions ---
chown -R www-data:www-data "${DOCROOT}"
chmod -R 755 "${DOCROOT}"

# --- Start Apache ---
echo "Starting Apache on port ${PORT}..."
exec apache2-foreground