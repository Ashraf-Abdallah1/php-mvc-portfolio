# Gebaseerd op PHP 8.2 met Apache
FROM php:8.2-apache

# Installeer de PDO MySQL extensie
RUN docker-php-ext-install pdo pdo_mysql

# Zet de werkdirectory
WORKDIR /var/www

# Kopieer alle bestanden van de lokale projectmap naar de container
COPY . .

# Stel de DocumentRoot in op /var/www/public
RUN sed -i 's#/var/www/html#/var/www/public#' /etc/apache2/sites-available/000-default.conf

# Sta .htaccess overschrijvingen toe
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Schakel mod_rewrite in
RUN a2enmod rewrite

# Railway geeft een dynamische poort via de $PORT variabele.
# Apache luistert standaard op poort 80, maar we moeten overschakelen naar $PORT.
RUN sed -i "s/Listen 80/Listen \${PORT}/" /etc/apache2/ports.conf && \
    echo "ServerName localhost:\${PORT}" >> /etc/apache2/apache2.conf

# Stel environment variabelen in voor database connectie (pas aan naar jouw wensen)
# Railway vult MYSQL_URL, MYSQLHOST, MYSQLPORT, MYSQLDATABASE, MYSQLUSER, MYSQLPASSWORD
ENV DB_HOST=${MYSQLHOST} \
    DB_PORT=${MYSQLPORT} \
    DB_USER=${MYSQLUSER} \
    DB_PASSWORD=${MYSQLPASSWORD} \
    DB_NAME=${MYSQLDATABASE}

# Exposeer de poort (Railway gebruikt automatisch $PORT, maar EXPOSE is voor documentatie)
EXPOSE 80

# Start Apache op de voorgrond (default CMD van de base image)
CMD ["apache2-foreground"]