FROM php:apache

# Cache bust (handig om oude lagen te vermijden bij herhaald bouwen)
RUN echo "Cache bust: 1"

# Systeemafhankelijkheden
RUN apt-get update && apt-get install -y git unzip

# PHP-extensies
RUN docker-php-ext-install pdo_mysql

# MPM-configuratie: schakel alleen de ongewenste MPM's uit, zet de juiste aan
RUN a2dismod mpm_event || true
RUN a2dismod mpm_worker || true
RUN a2enmod mpm_prefork rewrite

# Pas Apache-configuratie aan: AllowOverride All
RUN sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

# Verander de document root naar /var/www/html/public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Werkdirectory
WORKDIR /var/www/html

# Kopieer de hele applicatie
COPY . /var/www/html

# Installeer Composer en voer installatie uit (alleen productie-afhankelijkheden)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Zet de juiste permissies voor Apache (www-data)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p /var/www/html/database \
    && chown -R www-data:www-data /var/www/html/database \
    && chmod -R 775 /var/www/html/database