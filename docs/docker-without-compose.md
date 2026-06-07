#  Project starten

1. Vereisten

a. Docker Desktop geïnstalleerd

b. Dockerfile


# Optie 2  Docker zonder Docker Compose


### De applicatie kan ook handmatig worden gestart met Docker.

## Docker image bouwen

docker build -t itdp-Ashraf-Abdallah1 .

## Docker network aanmaken

docker network create mysql-pma-network


## MySQL container starten

docker run -d \
--name db \
--network mysql-pma-network \
-e MYSQL_ROOT_PASSWORD=root \
-e MYSQL_DATABASE=test_db \
-e MYSQL_USER=db_user \
-e MYSQL_PASSWORD=db_password \
-v dbdata:/var/lib/mysql \
mysql:8

## Webserver container starten

De webcontainer gebruikt dezelfde database-instellingen als in .env.local.

docker run -d \
--name web \
--network mysql-pma-network \
-p 80:80 \
-e APP_ENV=development \
-e VIEWS_PATH=app/views \
-e DB_HOST=db \
-e DB_NAME=test_db \
-e DB_USER=db_user \
-e DB_PASSWORD=db_password \
-e DB_PORT=3306 \
-v ${PWD}:/var/www \
itdp-Ashraf-Abdallah1


## Database migratie uitvoeren
docker exec -it web php /var/www/migrate.php migrate

## Applicatie toegang

Webapp: http://localhost

phpMyAdmin:http://localhost:8081


## phpMyAdmin login
Username: db_user

Password: db_password