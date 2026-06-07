#  Project starten

1. Vereisten

a. Docker Desktop geïnstalleerd

b. Docker Compose beschikbaar



# Optie 1 starten met docker-compose.yaml (Makelijkste)

## Build en start containers

Start het project met: docker compose up --build


## Database informatie
De database draait in een aparte Docker container.
Default instellingen (.env.local)

DB_HOST=db

DB_NAME=test_db

DB_PORT=3306

DB_USER=db_user

DB_PASSWORD=db_password

APP_ENV=local

VIEWS_PATH=app/views


## Database Architectuur

De applicatie maakt gebruik van het Repository Pattern.

Repositories communiceren met de MySQL database via PDO.
Hierdoor blijft de businesslogica losgekoppeld van de database-implementatie.


## Database migraties
1. Locatie migrations  =  /database
2. Migraties uitvoeren (Docker) = docker compose exec web php /var/www/migrate.php migrate
3. Migraties uitvoeren (lokaal)  = php migrate.php migrate


## Docker Volumes

De applicatie maakt gebruik van Docker volumes voor persistente opslag van databasegegevens.

### Volume:

dbdata

Dit volume wordt gekoppeld aan:

/var/lib/mysql

Hierdoor blijven alle databasegegevens behouden wanneer containers worden gestopt of verwijderd. Alleen wanneer het volume expliciet wordt verwijderd, gaan de gegevens verloren.


### Volume verwijderen :
docker volume rm dbdata
Hier mee wordt alle data verwijderd en kan niet ongedaan worden


## Applicatie toegang

Webapp: http://localhost

phpMyAdmin:http://localhost:8081


## phpMyAdmin login
Username: db_user

Password: db_password


## Start & Stop contanier
a. start =>  docker compose up

b.  stop => docker compose down