#!/bin/bash
set -e

echo "Starting entrypoint script..."

# --- Map Railway MySQL variables to the DB_* names your app expects ---
export DB_HOST=${MYSQLHOST}
export DB_PORT=${MYSQLPORT}
export DB_USER=${MYSQLUSER}
export DB_PASSWORD=${MYSQLPASSWORD}
export DB_NAME=${MYSQLDATABASE}

# (Optional) Also set APP_ENV if needed
export APP_ENV=${APP_ENV:-production}

echo "Database configuration:"
echo "  DB_HOST: ${DB_HOST}"
echo "  DB_PORT: ${DB_PORT}"
echo "  DB_NAME: ${DB_NAME}"
echo "  DB_USER: ${DB_USER}"
# DO NOT log the password

# --- Fix for Apache MPM conflicts ---
a2dismod mpm_event 2>/dev/null || true
a2dismod mpm_worker 2>/dev/null || true
a2dismod mpm_prefork 2>/dev/null || true
a2enmod mpm_prefork

# --- Enable mod_rewrite (needed for your .htaccess) ---
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

# --- Ensure permissions ---
chown -R www-data:www-data "${DOCROOT}"
chmod -R 755 "${DOCROOT}"

# --- Start Apache ---
echo "Starting Apache on port ${PORT}..."
exec apache2-foreground