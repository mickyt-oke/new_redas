# NIS REDAS

Laravel-based web application for the **NIS REDAS** project.

## Overview

This project is built with **Laravel 12** and **PHP 8.2**.  
It includes authentication, role-aware login flow, and protected dashboard routes for different user roles.

## Features

- User registration and login
- Login using **email** or **service number**
- Role selection during login:
  - `admin`
  - `zonal`
  - `state`
  - `officer`
- Protected routes using authentication middleware
- Role-protected admin dashboard route
- Logout with session invalidation and CSRF token regeneration

## Tech Stack

- PHP `^8.2`
- Laravel Framework `^12.0`
- Laravel Sanctum `^4.3`
- Vite (frontend build tooling)
- PHPUnit `^11.5`

## Prerequisites

Before setup, ensure you have:

- PHP 8.2+
- Composer
- Node.js and npm
- A database supported by Laravel (e.g., MySQL)

## Installation

1. Clone the repository.
2. Install PHP dependencies:

```bash
composer install
```

3. Install frontend dependencies:

```bash
npm install
```

4. Create environment file:

```bash
copy .env.example .env
```

5. Generate app key:

```bash
php artisan key:generate
```

6. Configure your database credentials in `.env`.
7. Run migrations:

```bash
php artisan migrate
```

## Running the Application

### Backend (Laravel server)

```bash
php artisan serve
```

### Frontend assets (Vite dev server)

```bash
npm run dev
```

For production assets:

```bash
npm run build
```

## Useful Commands

- Run tests:

```bash
php artisan test
```

- Format/check coding style (Laravel Pint):

```bash
./vendor/bin/pint
```

## Authentication and Role Notes

- Registration validates:
  - `name`
  - `service_number` (unique)
  - `role` (`admin|zonal|state|officer`)
  - `email` (unique)
  - `password` + confirmation
  - accepted terms

- Login accepts:
  - `login` (email or service number)
  - `password`
  - `role`

After successful login, redirection is role-based:
- `admin` → `/admin/dashboard`
- `zonal` → `/dashboard/zonal`
- `state` → `/dashboard/state`
- `officer` → `/dashboard`
- fallback → `/home`

## Route Summary (Web)

- `GET /` → welcome view
- `GET /home` → welcome view
- `GET /login` → login page (guest only)
- `POST /login` → login submit
- `GET /register` → register page (guest only)
- `POST /register` → register submit
- `POST /logout` → logout
- `GET /user/dashboard` → authenticated users
- `GET /admin/dashboard` → authenticated users with admin role

## Project Structure (high level)

- `app/Http/Controllers/` - application controllers
- `app/Models/` - Eloquent models
- `database/migrations/` - DB schema changes
- `resources/views/` - Blade/PHP views
- `resources/js/`, `resources/css/` - frontend assets
- `routes/` - route definitions

## License

This project is licensed under the MIT License (per `composer.json`).
