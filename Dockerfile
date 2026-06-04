FROM php:8.2-apache

# cache bust: v2
RUN docker-php-ext-install pdo pdo_mysql

# Fix: disable conflicting MPMs, enable only prefork
RUN a2dismod mpm_event mpm_worker 2>/dev/null || true && \
    a2enmod mpm_prefork

RUN a2enmod rewrite

RUN sed -i 's#/var/www/html#/var/www/public#' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

COPY . /var/www/

ENV PORT=8080
EXPOSE 8080

#RUN sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
#RUN sed -i "s/:80/:${PORT}/" /etc/apache2/sites-enabled/000-default.conf
RUN sed -i "s/Listen 80/Listen 8080/" /etc/apache2/ports.conf
RUN sed -i "s/:80/:8080/" /etc/apache2/sites-enabled/000-default.conf

CMD ["apache2-foreground"]