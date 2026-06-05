FROM php:8.2-apache

# Install PHP extensions (add others as needed)
RUN docker-php-ext-install pdo pdo_mysql

# Set working directory and copy your application files
WORKDIR /var/www
COPY . .

# Optional: Configure Apache DocumentRoot if your entrypoint is not /var/www/html
# RUN sed -i 's#/var/www/html#/var/www/public#' /etc/apache2/sites-available/000-default.conf

# --- Copy and setup the new entrypoint script ---
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# --- Configure the container to use the new entrypoint ---
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

# The CMD instruction can be a fallback, but the entrypoint above will handle startup
# CMD ["apache2-foreground"]