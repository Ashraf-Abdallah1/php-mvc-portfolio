FROM php:8.2-apache

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Optionally install Composer (if you need it)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory (consistent with COPY)
WORKDIR /var/www

# Copy your entire project
COPY . .

# If you have a composer.json, run install (adjust as needed)
# RUN composer install --no-dev --optimize-autoloader

# Copy and set entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]