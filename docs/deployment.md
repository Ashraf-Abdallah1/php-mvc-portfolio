# Deployment Documentatie – Railway (Web Service + MySQL)

Deze applicatie is gedeployed op Railway, een Platform-as-a-Service (PaaS) waarmee Docker-gebaseerde applicaties eenvoudig gehost kunnen worden.

De productieomgeving bestaat uit:

- 1 Web Service (PHP 8.2 + Apache)
- 1 MySQL Database Service
- Automatische CI/CD pipeline via GitHub Actions
- Automatische deployments naar Railway bij succesvolle CI
- HTTPS + eigen Railway-domein

---

## 1. Productie Deployment

De applicatie draait live op Railway via:

https://itdp-ashraf-abdallah1-production.up.railway.app

Railway verzorgt automatisch:

- HTTPS certificaten
- Autoscaling (serverless)
- Logging
- Container restarts bij crashes

---

## 2. Architectuur van de productieomgeving

### Web Service

De Web Service draait in een Docker-container met:

- PHP 8.2 + Apache
- PDO + PDO MySQL
- Rewrite module
- DocumentRoot: /var/www/public
- Custom entrypoint voor Apache configuratie

---

### Database Service

De Railway MySQL service bestaat uit:

- MySQL 8
- Persistente storage
- Automatisch gegenereerde credentials

---

### Environment Variables

De Web Service is gekoppeld aan de MySQL service via Railway environment variables:

| Variable | Waarde (voorbeeld) |
|----------|-------------------|
| DB_HOST | zephyr.proxy.rlwy.net |
| DB_NAME | railway |
| DB_USER | root |
| DB_PASSWORD | qAIYOTFCVdnoEKFSGFEzZCOmepBGcJpW |
| DB_PORT | 19163 |
| APP_ENV | production |
| VIEWS_PATH | app/views |

Deze variabelen moeten handmatig worden ingesteld in Railway onder Variables.

---

## 3. Deployment Workflow

### Automatische Deploys

De Web Service is gekoppeld aan de GitHub-repository:

HZ-ICT1-2526/itdp-Ashraf-Abdallah1

Elke push naar de `main` branch triggert:

- GitHub Actions CI pipeline
- Railway wacht op CI-resultaat
- Bij succes → automatische deploy
- Bij falen → deploy wordt geblokkeerd

---

## 4. Continuous Integration (CI)

De CI pipeline draait bij elke push naar `main` en bevat de volgende controles:

### PHPStan – Level 8

Statische analyse op het hoogste niveau:

```bash
vendor/bin/phpstan analyse --level=8 src
```

### PHPCS – PSR-12

Code style controle volgens PSR-12:

vendor/bin/phpcs --standard=PSR12 src

### PHPUnit

Unit tests:

```bash
vendor/bin/phpunit --testdox --colors=always --coverage-text tests
```

### Deptrac – Dependency Rules

Architectuur validatie:

```bash
vendor/bin/deptrac analyse
```

### CI Resultaten


Als eenn van de checks faalt:


Pipeline stopt

Railway ontvangt “CI failed”

Er wordt niet gedeployed

Developer wordt geïnformeerd via GitHub


Als alle checks slagen:

Railway triggert automatisch een deploy
Nieuwe versie wordt live gezet

## 5. Nieuwe versie releasen
### Stap 1 – Code aanpassen

```bash
git add .
git commit -m "Nieuwe release"
git push
```

### Stap 2 – CI draait automatisch

GitHub Actions voert uit:

a. PHPStan

b. PHPCS

c. PHPUnit

d. Deptrac


### Stap 3 – Railway deployt automatisch


Alleen bij succesvolle CI.

### Stap 4 – Live zetten

Railway:


a. Bouwt de Dockerfile

b. Start een nieuwe container

c. Zet de nieuwe versie live


## Samenvatting

| Onderdeel                | Status |
|-------------------------|--------|
| Deployment              | Railway Web Service + MySQL |
| CI Checks               | PHPStan 8, PHPCS PSR-12, PHPUnit, Deptrac |
| Auto Deploy             | Ja, na succesvolle CI |
| Deploy blokkade bij fouten | Ja |
| HTTPS                   | Automatisch via Railway |
| GitHub koppeling        | Ja |



