FROM php:8.4-apache

RUN apt-get update && apt-get install -y git unzip

RUN docker-php-ext-install pdo_mysql

RUN a2enmod rewrite

RUN sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

COPY . .

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --optimize-autoloader

RUN ls -la /etc/apache2/mods-enabled/

CMD ["apache2-foreground"]