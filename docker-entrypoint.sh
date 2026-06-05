#!/bin/bash
set -e

echo "Starting entrypoint script..."

# MPM fix
a2dismod mpm_event 2>/dev/null || true
a2dismod mpm_worker 2>/dev/null || true
a2dismod mpm_prefork 2>/dev/null || true
a2enmod mpm_prefork

# Port
PORT=${PORT:-8080}
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
echo "ServerName localhost:${PORT}" >> /etc/apache2/apache2.conf

# Force DocumentRoot
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

# Enable PHP error logging
echo "log_errors = On" >> /usr/local/etc/php/conf.d/docker-php.ini
echo "error_log = /proc/self/fd/2" >> /usr/local/etc/php/conf.d/docker-php.ini
echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php.ini

# Ensure directory and test file
mkdir -p "$DOCROOT"
echo "<?php echo 'PHP OK'; phpinfo(); ?>" > "$DOCROOT/index.php"
chown -R www-data:www-data "$DOCROOT"
chmod -R 755 "$DOCROOT"

echo "Starting Apache on port ${PORT}..."
exec apache2-foreground