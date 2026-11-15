# PHP MVC Portfolio

A personal study portfolio built as a **PHP 8 MVC web application** on a small custom framework, without Laravel or Symfony. It shows my blogs, grades and study progress from my first year of HBO-ICT at HZ University of Applied Sciences.

> Individual first-year project (2025–2026). The goal was to understand what a framework does under the hood: routing, dependency injection, middleware, repositories and testing.

## Features

- **Authentication** with register, login, logout and a guest login
- **Roles**: admin and user, enforced by auth and admin middleware (401 and 403 pages)
- **Blog**: create, edit and delete posts as admin, with a delete confirmation
- **JSON API**: `GET /api/blogs` and `GET /api/blogs/{id}`, used by the front end to load posts with `fetch`
- **Dashboard**: courses, grades and study credits, editable by the admin
- **Profile** page and **FAQ** page
- **Database migrations** via a small CLI command

## Tech

| Area | Used |
|---|---|
| Back end | PHP 8.2, PDO, Twig templates |
| Database | MySQL 8 |
| Front end | Bootstrap, JavaScript, Chart.js |
| Infrastructure | Docker, Docker Compose, Apache, phpMyAdmin |
| Quality | PHPUnit, PHPStan, PHP_CodeSniffer, Deptrac |
| CI/CD | GitHub Actions, deployed on Railway |

## Architecture

```
app/
├── Controllers/     request handling, one controller per feature
├── Middleware/      AuthMiddleware, AdminMiddleware
├── Models/          Blog, User, Profile, Dashboard
├── Repositories/    interfaces and PDO implementations
├── Services/        AuthService
└── views/           Twig templates
src/                 the framework: Kernel, Router, ServiceContainer, Request, Response
database/            SQL migrations
tests/               unit tests, integration tests and dummy repositories
docs/                Docker and deployment guides, wireflows
```

- **Repository pattern**: controllers depend on repository interfaces, so the tests swap in in-memory dummy repositories instead of a real database
- **Dependency injection** through a service container
- **Layer rules checked by Deptrac**, so controllers cannot reach the database directly

## Run it locally

Requires Docker Desktop.

```bash
git clone https://github.com/Ashraf-Abdallah1/php-mvc-portfolio.git
cd php-mvc-portfolio
docker compose up --build
docker compose exec web php /var/www/migrate.php migrate
```

- Application: http://localhost
- phpMyAdmin: http://localhost:8081

More detail: [with Docker Compose](docs/docker-with-compose.md), [without Docker Compose](docs/docker-without-compose.md) and [deployment on Railway](docs/deployment.md).

## Tests and code quality

```bash
vendor/bin/phpunit --testdox tests
vendor/bin/phpstan analyse
vendor/bin/phpcs
vendor/bin/deptrac
```

The same four checks run in GitHub Actions on every push and pull request.

## Design

Wireflows for both roles are in [`docs/wireflows`](docs/wireflows).

## Credits

The framework in `src/` is based on the Maestro teaching framework by Frans Blauw (HZ University of Applied Sciences). The application in `app/`, the tests, the Docker setup and the deployment are my own work.
