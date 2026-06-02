# Over dit project

Dit project is een PHP MVC applicatie die draait in Docker.
De applicatie gebruikt:

PHP 8.2 + Apache
MySQL 8
phpMyAdmin
PDO

Dit portfolio is ontwikkeld tijdens het eerste jaar van de opleiding HBO-ICT.
De applicatie biedt een overzicht van mijn studieactiviteiten, waaronder blogs,
behaalde cijfers, en studievoortgang.
Het project laat mijn Cijfers en ervaringen zien gedurende het eerste studiejaar.

#  Project starten

1. Vereisten

a. Docker Desktop geïnstalleerd

b. Docker Compose beschikbaar



# Build en start containers

Start het project met: docker compose up --build


# Database informatie
De database draait in een aparte Docker container.
Default instellingen (.env.local)

DB_HOST=db

DB_NAME=test_db

DB_USER=db_user

DB_PASSWORD=db_password

APP_ENV=local

VIEWS_PATH=app/views


# Database migraties
1. Locatie migrations  =  /database
2. Migraties uitvoeren (Docker) = docker compose exec web php /var/www/migrate.php migrate
3. Migraties uitvoeren (lokaal)  = php migrate.php migrate



# Applicatie toegang

Webapp: http://localhost

phpMyAdmin:http://localhost:8081


# phpMyAdmin login
Username: db_user

Password: db_password


# Start & Stop contanier 
 a. start =>  docker compose up

 b.  stop => docker compose down


