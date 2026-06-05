FROM php:8.2-apache

# Install PDO MySQL extension
RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www
COPY . .

# Configure Apache: DocumentRoot, .htaccess, mod_rewrite
RUN sed -i 's#/var/www/html#/var/www/public#' /etc/apache2/sites-available/000-default.conf && \
    sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf && \
    a2enmod rewrite

# --- FORCE only mpm_prefork and remove any other MPM ---
RUN set -eux; \
    # Disable all possible MPMs
    a2dismod mpm_event mpm_worker mpm_prefork 2>/dev/null || true; \
    # Delete their load files from mods-available and mods-enabled
    rm -f /etc/apache2/mods-available/mpm_event.load \
          /etc/apache2/mods-available/mpm_worker.load \
          /etc/apache2/mods-enabled/mpm_event.load \
          /etc/apache2/mods-enabled/mpm_worker.load \
          /etc/apache2/mods-available/mpm_prefork.load \
          /etc/apache2/mods-enabled/mpm_prefork.load; \
    # Enable only mpm_prefork (and create its load file)
    a2enmod mpm_prefork; \
    # Verify only prefork is enabled
    apache2ctl -M | grep mpm

# Startup script for Railway: dynamic port and DB environment mapping
RUN echo '#!/bin/bash\n\
PORT=${PORT:-8080}\n\
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf\n\
echo "ServerName localhost:${PORT}" >> /etc/apache2/apache2.conf\n\
\n\
# Map Railway MySQL variables to DB_* (adjust to your app needs)\n\
export DB_HOST=${MYSQLHOST}\n\
export DB_PORT=${MYSQLPORT}\n\
export DB_USER=${MYSQLUSER}\n\
export DB_PASSWORD=${MYSQLPASSWORD}\n\
export DB_NAME=${MYSQLDATABASE}\n\
\n\
# Start Apache\n\
apache2-foreground' > /start.sh && chmod +x /start.sh

EXPOSE 80
CMD ["/start.sh"]