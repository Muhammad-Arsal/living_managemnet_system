# Living Management System

Laravel-based portal for managing living / housing operations across three separate panels: **Admin**, **Staff**, and **Council**.

## Requirements

- PHP 8.3+
- Composer
- MySQL / MariaDB (or SQLite for local)
- Node.js & npm (optional, for Vite assets)

## Quick start

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure your database in `.env`, then:

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Optional frontend assets:

```bash
npm install
npm run build
# or: npm run dev
```

## Login URLs

| Panel   | URL              |
|---------|------------------|
| Admin   | `/admin/login`   |
| Staff   | `/staff/login`   |
| Council | `/council/login` |

### Seeded accounts

After `php artisan migrate --seed`:

| Panel   | Email                 | Password  |
|---------|-----------------------|-----------|
| Admin   | `admin@example.com`   | `password` |
| Staff   | `staff@example.com`   | `password` |
| Council | `council@example.com` | `password` |

## Architecture

This project does **not** use a shared users table or RBAC. Each panel has its own auth model, guard, profile table, routes, and views.

| Entity  | Auth table | Profile table        | Guard     |
|---------|------------|---------------------|-----------|
| Admin   | `admins`   | `admin_profiles`    | `admin`   |
| Staff   | `staff`    | `staff_profiles`    | `staff`   |
| Council | `councils` | `council_profiles`  | `council` |

### Layers

| Layer        | Responsibility                         |
|--------------|----------------------------------------|
| Controller   | HTTP only — Form Request + Service     |
| Form Request | Validation only                        |
| Service      | Business logic                         |
| Repository   | Database / Eloquent (via interfaces)   |
| Model        | Relationships, casts, accessors        |

### Views & routes

```
resources/
  frontend/                 # Public views → frontend::*
  backend/
    admin/                  # backend::admin.*
    staff/                  # backend::staff.*
    council/                # backend::council.*

routes/
  web.php                   # entry + require() each module
  backend/admin/{module}.php
  backend/staff/{module}.php
  backend/council/{module}.php
```

Full conventions live in [`rules.md`](rules.md).

## Features

- Separate Admin / Staff / Council dashboards
- Email verification, forgot password, welcome / set-password flows
- Admin modules: Staff, Council, Settings (Admins, Email Templates, Site Settings)
- Site logo / favicon settings
- Fully responsive backend UI

## Model auditing

All Eloquent models implement `OwenIt\Auditing\Contracts\Auditable` via `App\Models\Concerns\AuditsModelChanges`.

- Package: [`owen-it/laravel-auditing`](https://github.com/owen-it/laravel-auditing)
- Events tracked: `created`, `updated`, `deleted`, `restored`
- Sensitive fields excluded by default: `password`, `remember_token`
- Acting user resolved from guards: `admin`, `staff`, `council`, `web`
- Activity Log UI: **Admin → Settings → Audit Logs** (`/admin/settings/audit-logs`)

When adding a new model, always:

```php
use App\Models\Concerns\AuditsModelChanges;
use OwenIt\Auditing\Contracts\Auditable;

class Example extends Model implements Auditable
{
    use AuditsModelChanges;
}
```

Console / seeder actions are not audited by default (`audit.console = false` in `config/audit.php`).

## Project structure (key paths)

```
app/
  Http/Controllers/Backend/{Admin|Staff|Council}/
  Http/Requests/Backend/{Admin|Staff|Council}/
  Models/
  Repositories/
  Services/{Admin|Staff|Council}/
config/
  audit.php
  auth.php
database/migrations/
resources/backend/
routes/backend/
```

## Useful commands

```bash
php artisan migrate
php artisan db:seed
php artisan route:list
php artisan test
vendor/bin/pint
```

## License

Proprietary / project-specific — update this section if you publish under an open-source license.
