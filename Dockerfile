FROM php:8.2-apache

# Cache bust en installeer systeem-packages
RUN apt-get update && apt-get install -y git unzip

RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite

# Pas de DocumentRoot en AllowOverride hier aan, zodat de container direct klaar is voor gebruik
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Kopieer alle bestanden naar de container
COPY . /var/www/html

# Installeer Composer dependencies (geoptimaliseerd voor productie)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p /var/www/html/database \
    && chown -R www-data:www-data /var/www/html/database \
    && chmod -R 775 /var/www/html/database

CMD ["apache2-foreground"]