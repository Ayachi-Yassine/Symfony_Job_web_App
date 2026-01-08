<!-- .github/copilot-instructions.md - Guidance for AI coding agents working on this Symfony app -->

# Copilot instructions for JobEntry (Symfony 7.4)

**Purpose:** Provide concise, actionable knowledge to make AI coding agents productive immediately in this repository.

- **Big Picture:** This is a Symfony 7.4 web application (backend + Twig frontend). The HTTP entrypoint is `public/index.php` and the app kernel is `src/Kernel.php`. Business logic lives in `src/Controller`, domain models in `src/Entity`, and DB access in `src/Repository`.
- **Templates & Assets:** Twig templates live under `templates/` (global layout: `templates/base.html.twig`). Static assets are in `public/` (`public/css`, `public/js`, `public/img`, `public/lib`). Uploads are stored in `uploads/` (e.g. `uploads/applications/`, `uploads/cv/`) — these paths must be writable and may need special handling in cloud deployments.

- **Service wiring & DI:** Services use autowire/autoconfigure per `config/services.yaml`. When adding services, prefer constructor injection and follow existing class namespaces (PSR-4 `App\` => `src/`.

- **Routing convention:** Controllers use PHP attributes (e.g., `#[Route(...)]`) and controller methods are auto-imported via `config/routes.yaml` which references `routing.controllers`. Look at `src/Controller/*Controller.php` for examples.

- **Database & Migrations:** Doctrine ORM is used. Entities are under `src/Entity/`. Migrations are tracked in `migrations/` (e.g., `migrations/Version20251129141657.php`). Typical commands:
  - `composer install`
  - `php bin/console doctrine:database:create --if-not-exists`
  - `php bin/console doctrine:migrations:migrate --no-interaction`

- **Key files to inspect for patterns and examples:**
  - `composer.json` (dependencies, auto-scripts)
  - `public/index.php` (entrypoint)
  - `config/services.yaml` (DI defaults: autowire/autoconfigure)
  - `config/packages/security.yaml` (auth rules)
  - `src/Security/UserProvider.php` (custom user provider)
  - `src/Command/CreateAdminCommand.php` (console command pattern)
  - `src/Controller/JobController.php`, `src/Controller/JobApplicationController.php` (request handling examples)
  - `templates/base.html.twig` (layout conventions)

- **Build / Dev workflows (exact commands):**
  - Install deps: `composer install --no-interaction --optimize-autoloader`
  - Run migrations: `php bin/console doctrine:migrations:migrate --no-interaction`
  - Dev server: `symfony server:start` (recommended) or `php -S 0.0.0.0:8000 -t public` (builtin)
  - Clear cache: `php bin/console cache:clear`

- **Deployment notes (Render or similar):**
  - This is a PHP/Symfony app — pick PHP runtime on the host (not Node). Example Render build/start settings:
    - Build command: `composer install --no-dev --no-interaction --optimize-autoloader && php bin/console doctrine:migrations:migrate --no-interaction && php bin/console assets:install public`
    - Start command (simple): `php -S 0.0.0.0:$PORT -t public` (not for high-traffic production)
  - Environment variables required: at minimum `DATABASE_URL`, `APP_ENV`, `APP_SECRET`. Check `.env`/`config/packages/*` for other env keys.
  - Upload directories under `uploads/` are ephemeral on many PaaS free tiers; for persistent uploads use a shared disk or external storage (S3) and update upload handling accordingly.

- **Project-specific conventions and patterns:**
  - Use Doctrine Entities in `src/Entity` with repositories in `src/Repository` (custom query logic goes in repository classes).
  - Controllers return `Response` and render templates via `$this->render('...')`; follow existing controller method structure for params and services.
  - Console commands exist under `src/Command` and are registered automatically by autoconfigure.
  - Notifications and user activities are modelled with `src/Entity/Notification.php` and `src/Entity/UserActivity.php` — follow their patterns for new activity/notification features.

- **What to avoid or preserve:**
  - Do not change routing import style — controllers are auto-imported via `config/routes.yaml`.
  - Preserve service autowiring conventions; avoid manual container access unless necessary.
  - Keep frontend structure (Twig + public assets) intact; do not move templates into controllers.

- **Debug & logs:**
  - Cache & logs: `var/cache/`, `var/log/`. To reproduce production issues, inspect `var/log/prod.log` and `var/log/dev.log`.

- **When making code changes:**
  - Add or update migrations when schema changes are required and run them locally to verify.
  - Run `php bin/console cache:clear` and check templates in `templates/` after changes.
  - For views, reference `templates/base.html.twig` and the `templates/*` directories to reuse blocks and CSS structure.

- If anything here is unclear or you'd like more detail (e.g., specific controller examples or Render configuration snippets), say which area to expand and I will iterate.

FROM php:8.2-fpm

WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl libpq-dev libicu-dev \
    && docker-php-ext-install pdo_mysql intl \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy app files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Expose port
EXPOSE 8000

# Run migrations and start server
CMD php bin/console doctrine:migrations:migrate --no-interaction && \
    php -S 0.0.0.0:8000 -t public
