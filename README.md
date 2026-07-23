# NIS REDAS

Laravel-based web application for the **NIS REDAS** project.

## Overview

NIS REDAS is a Laravel 12 application with both web and API interfaces. It supports role-aware access control, OTP-backed API authentication, JWT access/refresh tokens, database-backed notification delivery, and templated transactional email via Resend.

---

## Tech Stack

### Core Platform
- **PHP** `^8.2`
- **Laravel Framework** `^12.0`
- **Composer** for PHP dependency management

### Authentication, Security, and Identity
- **Custom JWT implementation** using `firebase/php-jwt` (`^7.0`)
- **Laravel Sanctum** (`^4.3`) available in the stack
- **Web TOTP multi-factor authentication** using `pragmarx/google2fa` and `bacon/bacon-qr-code` (setup, challenge, backup codes)
- **OTP-based second-step verification** for API login (`otp/request`, `otp/verify`)
- **Refresh token rotation** persisted in database (`refresh_tokens`)
- **Rate limiting** on API and web auth paths (`throttle:api`, `throttle:database`)
- **Session authentication** for web routes (`auth`, `guest`, CSRF/session lifecycle)
- **ABAC geolocation enforcement** on login and session requests

### Messaging and Notifications
- **Resend** (`resend/resend-php`) as transactional email provider
- **Template-driven email rendering** using DB-backed email templates (`email_templates`)
- **In-app notifications** via `user_notifications` table and API endpoints

### Data Layer
- **Eloquent ORM**
- **Laravel Migrations + Seeders**
- Relational database supported by Laravel (commonly MySQL/MariaDB)

### Frontend and Asset Tooling
- **Vite** (`^7.0.7`) with Laravel Vite plugin (`^2.0.0`)
- **Tailwind CSS** (`^4.0.0`)
- **Bootstrap** (`^5.3.2`)
- **Font Awesome** (`^6.4.0`)
- **Chart.js** (`^4.4.0`)
- **Axios** for browser-side HTTP requests

### PWA Support
- **silviolleite/laravelpwa** (`^2.0`)

### Quality and Testing
- **PHPUnit** (`^11.5.3`)
- **Laravel Pint** (`^1.24`) for code style
- **Mockery** + **Collision** in dev toolchain

---

## Backend Technical Documentation

### 1) Backend Architecture

The backend follows standard Laravel layering with domain-specific customizations:

- **Controllers**: Orchestrate web/API request handling.
  - Web auth/flows: `AuthController`, `AuthTokenController`, `MfaController`
  - API auth/OTP: `ApiAuthController`, `ApiOtpController`
  - Notifications: `ApiNotificationController`
- **Middleware**:
  - `JwtAccessTokenMiddleware`: Validates bearer JWT access tokens and hydrates authenticated user context
  - `CheckAccess`: Dynamic authorization guard using category/location/role/min-level rules
  - `RequireMfaPending`: Ensures MFA routes only run while a login is pending
- **Services**:
  - `JwtService`: JWT issue/decode/expiry logic
  - `MfaService`: TOTP secret generation, QR code rendering, and code verification
  - `Messaging/ResendMailService`: Sends templated mail via Resend transport
  - `Messaging/TemplateRenderer`: Resolves template variables
- **Models**:
  - `User`, `RefreshToken`, `OtpCode`, `AuthToken`, `EmailTemplate`, `UserNotification`, etc.
- **Routing**:
  - `routes/web.php` for session-authenticated browser flows
  - `routes/api.php` for throttle-protected API endpoints

### 2) Authentication and Authorization Design

### 2.1 Web Authentication (Session-based)
- Login supports `email` or `service_number`.
- After successful credentials and role/ABAC checks, the user is staged for MFA:
  - Users without MFA configured are redirected to `/mfa/setup` to scan a QR code and verify a TOTP code.
  - Users with MFA enabled are redirected to `/mfa/challenge` to enter a TOTP or backup code.
  - Successful verification completes login and redirects to the role-based dashboard.
- Uses Laravel session auth (`Auth::login`) and standard logout session invalidation.
- Post-login redirect is based on `user_category` and access tier.
- Registration stores normalized access profile values and sends a verification magic link token.

### 2.2 Web MFA (TOTP) Flow
The web MFA flow is enforced on every login:

1. **Stage**: `POST /login` validates credentials, runs ABAC/geolocation checks, and stores a pending user ID in the session.
2. **Setup**: `GET /mfa/setup` generates an encrypted TOTP secret and QR code.
3. **Verify setup**: `POST /mfa/setup` verifies the first TOTP code, enables MFA, and displays single-use backup codes.
4. **Complete**: `POST /mfa/complete` finishes login and redirects to the dashboard.
5. **Challenge**: For returning users, `GET /mfa/challenge` and `POST /mfa/challenge` verify a TOTP or backup code before login completes.
6. **Cancel**: `POST /mfa/cancel` clears the pending MFA session and returns to login.

Key implementation files:
- `app/Http/Controllers/MfaController.php`
- `app/Services/MfaService.php`
- `app/Http/Middleware/RequireMfaPending.php`
- `resources/views/mfa/setup.blade.php`, `challenge.blade.php`, `backup-codes.blade.php`
- `database/migrations/2026_07_22_000004_add_mfa_to_users_table.php`

### 2.2 API Authentication (JWT + OTP)
API login is a staged flow:

1. **POST `/api/login`**
   - Validates credentials and role.
   - Requires verified email.
   - Generates 4-digit OTP, stores hash in `otp_codes`, emails OTP via template.
   - Returns `OTP_REQUIRED`.
2. **POST `/api/otp/verify`** with `purpose=login`
   - Verifies OTP hash and expiry.
   - Enforces attempt limits.
   - On success:
     - Issues short-lived **access token** JWT (`type=access`)
     - Issues long-lived **refresh token** JWT (`type=refresh`)
     - Stores refresh token hash + metadata in `refresh_tokens`

3. **POST `/api/refresh`**
   - Validates refresh JWT and DB record.
   - Revokes old refresh token (rotation hardening).
   - Issues new access + refresh tokens.

4. **POST `/api/logout`**
   - Revokes refresh token record when provided.

### 2.3 Access Control Model
Authorization combines four dimensions:
- `user_category` (e.g., `state_user`, `desk_admin`, `directorate_user`, `super_admin`)
- `primary_location_type` (e.g., `state`, `directorate`, `zonal`, `headquarters`)
- semantic/legacy role compatibility
- integer `access_level` threshold

`CheckAccess` middleware enforces deny-by-default constraints in route middleware declarations such as:

```php
'access:category=state_user|desk_admin,location=state,role=user|officer|minLevel=0'
```

### 3) JWT Implementation Details

`App\Services\JwtService` responsibilities:
- Build access token payload:
  - `iss`, `sub`, `role`, `type=access`, `jti`, `iat`, `exp`
- Build refresh token payload:
  - `type=refresh` and longer expiry
- Decode and validate HS256 JWT signatures

Environment-sensitive values:
- `JWT_SECRET`
- `JWT_ISSUER`
- `JWT_ACCESS_TTL`
- `JWT_REFRESH_TTL`

`JwtAccessTokenMiddleware`:
- Requires `Authorization: Bearer <token>`
- Rejects invalid/expired/non-access tokens
- Resolves `sub` to `User` and sets auth context for downstream handlers

### 4) OTP and Email Delivery Flow

OTP flow details (`ApiOtpController`, `ApiAuthController`):
- OTPs are exactly 4 digits (leading zeros allowed).
- Stored as SHA-256 hash, not plaintext.
- Expiry and max attempts are env-configurable:
  - `OTP_TTL_SECONDS`
  - `OTP_MAX_ATTEMPTS`

Email delivery (`ResendMailService`):
- Fetches active template by key from `email_templates`
- Renders subject/body with `TemplateRenderer`
- Sends through Laravel `resend` mailer
- Requires `services.resend.key` / `RESEND_API_KEY`

Common templates referenced:
- `verify_email_magic_link`
- `otp_login`
- `otp_verify_email`

### 5) API Surface (Backend-focused)

From `routes/api.php` (under `throttle:api`):

### Public
- `POST /api/register` → registration (uses `AuthController@register`)
- `POST /api/login` → credentials check + OTP challenge
- `POST /api/otp/request` → request OTP
- `POST /api/otp/verify` → verify OTP and optionally issue JWTs
- `POST /api/refresh` → rotate refresh token + issue new token pair

### Protected by `JwtAccessTokenMiddleware`
- `GET /api/user`
- `GET /api/notifications`
- `GET /api/notifications/count`
- `POST /api/notifications/mark-all-read`
- `POST /api/notifications/{id}/read`
- `POST /api/logout`

### 6) Web Routing and Role-Segmented Areas

`routes/web.php` includes:
- Guest-only auth routes (`/login`, `/register`, password reset, verify-email)
- Access-controlled route groups for:
  - state users/officers
  - directorate users/admins
  - zonal/state supervisors
  - HQ admin
  - super admin
- Notification endpoints also exposed in session-authenticated web namespace for realtime-friendly UI polling.

### 7) Core Data Model (Backend-relevant)

- `users`: principal identity + access profile fields + MFA state (`mfa_secret`, `mfa_enabled`, `mfa_backup_codes`, etc.)
- `refresh_tokens`: refresh token lifecycle and revocation state
- `otp_codes`: OTP challenge records with attempts/expiry/consumption
- `auth_tokens`: magic-link/verification token lifecycle
- `email_templates`: dynamic template content and activation control
- `user_notifications`: notification feed + read state
- `nis_data`, `nis_directories`: project-specific domain data

### 8) Security Controls Summary

- Input validation across auth/OTP/MFA endpoints
- TOTP secret and backup codes encrypted at rest
- OTP hashing and expiry checks
- JWT type separation (access vs refresh)
- Refresh token DB revocation + rotation
- Route-level throttling
- Multi-dimensional access middleware checks
- Session invalidation + CSRF token regeneration on logout (web)

### 9) Local Development Setup

#### Prerequisites
- PHP 8.2+
- Composer
- Node.js + npm
- SQL database supported by Laravel

#### Installation
```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan migrate
```

Configure environment values in `.env` (database, mail/resend, JWT, OTP).

#### Run
```bash
php artisan serve
npm run dev
```

Build frontend assets:
```bash
npm run build
```

### 10) Testing and Quality

Run full test suite:
```bash
php artisan test
```

Run formatter/lint style:
```bash
./vendor/bin/pint
```

Relevant current tests include:
- auth login/registration feature tests
- MFA flow feature tests (`MfaFlowTest`)
- OTP API feature tests
- notifications web tests
- unit tests for template rendering

---

## Project Structure (high level)

- `app/Http/Controllers/` - web and API controllers
- `app/Http/Middleware/` - JWT and access control middleware
- `app/Models/` - Eloquent entities
- `app/Services/` - JWT + messaging services
- `database/migrations/` - schema evolution
- `database/seeders/` - seed data
- `resources/views/` - Blade templates
- `resources/js/`, `resources/css/` - frontend assets
- `routes/` - route definitions

## License

This project is licensed under the MIT License (per `composer.json`).
