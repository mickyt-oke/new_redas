# NIS REDAS — Technical Requirements Document (TRD)

**Project:** NIS REDAS — Returns Data Automation System
**Stack:** Laravel 12 · PHP 8.2 · MySQL · Blade + Vite · JWT · Resend · PWA

---

## 1. System architecture

Server-rendered monolith with a small companion JSON API:

```
┌────────────┐   HTTPS    ┌──────────────────────────────────────────┐
│  Browser    │──────────▶ │  routes/web.php (session auth, Blade)     │
│  (PWA)      │            │  Middleware: auth → access → abac.geo     │
└────────────┘            │                                          │
┌────────────┐   HTTPS    │  routes/api.php (JWT auth, JSON)          │
│  API client │──────────▶ │  Middleware: throttle:api → JwtAccess... │
└────────────┘            └──────────────┬───────────────────────────┘
                                          ▼
                    Controllers (app/Http/Controllers[/{Admin,Web}])
                                          ▼
                    Services (app/Services) — business logic
                    Models  (app/Models)    — Eloquent over MySQL
                                          ▼
              MySQL · DB sessions · DB cache · DB queue
                                          ▼
        External: Resend (email) · ip-api.com / ipapi.co (geolocation)
```

- **Routing:** `routes/web.php` (318 lines, session-auth Blade UI), `routes/api.php` (JWT-auth JSON API), registered in `bootstrap/app.php`.
- **Controllers:** `app/Http/Controllers/` — web auth (`AuthController`, `MfaController`, `AuthTokenController`), API (`ApiAuthController`, `ApiOtpController`, `ApiNotificationController`), workflow (`SubmissionReviewController`), dashboards (`Web/DashboardController`), HQ admin (`Admin/HqAdminController`, `AdminSettingController`, `AdminAuditLogController`, `UserManagementController`, `Admin/ConsolidationController`, `Admin/SuperAdminController`).
- **Services:** all business logic (workflow, geo, MFA, JWT, settings, reporting, mail) lives in `app/Services/` as mostly static classes.
- **Providers:** only `App\Providers\AppServiceProvider` — sets `Schema::defaultStringLength(191)` and rate limiters `api` (60/min) and `database` (30/min), keyed by user ID or IP.

## 2. Runtime requirements

| Component | Requirement |
|---|---|
| PHP | ^8.2 |
| Framework | Laravel ^12.0 |
| Database | MySQL (default, DB `redas_new`, utf8mb4, strict mode); SQLite for tests |
| Web server | Any Laravel-compatible (Apache `.htaccess` shipped in `public/`) |
| PHP extensions | ZipArchive (Excel writer), OpenSSL, PDO |
| Node | Vite 7 build pipeline (`npm run build`) |

Composer packages (runtime): `laravel/framework`, `laravel/sanctum`, `firebase/php-jwt ^7.0`, `pragmarx/google2fa`, `bacon/bacon-qr-code`, `barryvdh/laravel-dompdf`, `resend/resend-php 0.11`, `silviolleite/laravelpwa`.

## 3. Authentication & token lifecycle

Three parallel mechanisms:

### 3.1 Web (session) — mandatory MFA

1. `POST /login` → `AuthController::login` (`app/Http/Controllers/AuthController.php`): username = email or service number; rate-limited 5 attempts/60 s per identity+IP; disabled accounts rejected; failures audit-logged.
2. ABAC geo policy (`checkAbacPolicy`): settings-driven (`abac_enabled`, `abac_location_enforcement`, `abac_allowed_countries`, `abac_hq_bypass`); state resolution via `GeolocationService`.
3. User is staged, not logged in: `mfa.pending_user_id` + resolved location stored in session; redirect to MFA.
4. `MfaController`: first login → TOTP setup (Google2FA secret + BaconQrCode SVG 192 px QR, 8 single-use backup codes); returning → challenge (TOTP with 1-window drift, or backup code). Guarded by `mfa.pending` middleware.
5. `completeLogin()` → `Auth::login`, session stores `abac.location`, success audit-logged, redirect by `user_category`.
6. Sessions: **database** driver, 120-min lifetime.

### 3.2 API (JWT + OTP)

1. `POST /api/login` (`login`, `password`, `role`; role must match) → verified email required → 4-digit OTP emailed → `OTP_REQUIRED` response (no token yet).
2. OTP (`otp_codes`): SHA-256 hashed, TTL `OTP_TTL_SECONDS` (default 300 s), max `OTP_MAX_ATTEMPTS` (5).
3. `POST /api/otp/verify` (purpose `login`) → issues token pair via `JwtService`:
   - **Access:** HS256 JWT, claims `iss/sub/role/type=access/jti/iat/exp`, TTL `JWT_ACCESS_TTL` (default 900 s). Stateless; validated by `JwtAccessTokenMiddleware` (`type=access` check, loads user, `Auth::setUser`).
   - **Refresh:** JWT with `jti`, TTL `JWT_REFRESH_TTL` (default 30 d), persisted as SHA-256 hash in `refresh_tokens` with IP + user agent.
4. `POST /api/refresh` → rotation (old `jti` revoked, new pair issued). `POST /api/logout` → refresh token revoked.

### 3.3 Magic links

`AuthTokenController` + `auth_tokens` (SHA-256 hashed, single-use, TTL `AUTH_TOKEN_TTL_SECONDS` default 3600 s) for `email_verify` and `password_reset`, rendered from DB `email_templates` and sent via Resend. Password-reset request is enumeration-safe.

### 3.4 Sanctum

Installed and configured (`HasApiTokens`, `personal_access_tokens` table) but **no route uses `auth:sanctum`** — dormant. Consolidation candidate.

## 4. Authorization model

Middleware aliases (`bootstrap/app.php`):

| Alias | Class | Behavior |
|---|---|---|
| `role` | `CheckRole` | Simple `users.role` string match (legacy). |
| `access` | `CheckAccess` | Deny-by-default constraint parser, e.g. `access:category=desk_admin\|directorate_admin,location=state\|directorate,role=admin\|state\|minLevel=1`. Checks `user_category`, `primary_location_type`, role with legacy aliases, `access_level >= minLevel`. |
| `abac.geo` | `AbacGeolocationMiddleware` | Per-request geolocation enforcement: resolved IP state vs `User::requiredGeoState()` (explicit `geo_state` or state `primary_location_code`; directorate and CGIS-unit users exempt). Session-cached 5 min/IP. Violations → audit log + HTTP 403. |
| `mfa.pending` | `RequireMfaPending` | Requires valid `mfa.pending_user_id` session, else redirect to login. |

All protected web groups stack `auth` + `access:…` + `abac.geo`. The canonical category → (role, location, level 0–7) map is `AuthController::normalizeAccessProfile()` (mirrored in `Admin\UserManagementController`). Recognized categories (`User::ACCESS_CATEGORIES`): `state_user`, `desk_admin`, `directorate_user`, `directorate_admin`, `zonal_commander`, `cgis_unit_user`, `cgis_desk_admin`, `hq_admin`, `admin`, `super_admin`.

HQ tiers: `hq_admin` (level 5) is the final approver of all returns and owns `/admin/submissions` approve/reject, settings, audit log, and user management; `admin` (level 5) is view-only over the read-only `/admin/*` pages (dashboard, returns, archive, analytics, reports, `/admin/consolidation`); `super_admin` (level 6) is a strictly view-only executive dashboard (`/superadmin/*`). Both `hq_admin` and `admin` map to canonical role `admin` (the `CheckAccess` legacy-alias map includes `hq_admin → admin`).

## 5. Service layer contracts

| Service | Purpose | Key API | Dependencies |
|---|---|---|---|
| `AuditLogger` | Append-only audit trail | `log`, `logAuthAttempt`, `logAuthBlocked`, `logLogout`, `logCrud`, `logSettingChange` (static; auto IP/UA) | `AuditLog` |
| `JwtService` | HS256 JWT issue/decode | `fromEnv`, `issueAccessToken`, `issueRefreshToken`, `decode`, `isExpired` | `firebase/php-jwt` |
| `MfaService` | TOTP + backup codes | `generateSecret`, `renderQrCodeSvg`, `generateBackupCodes(8)`, `verifyCode` | google2fa, BaconQrCode |
| `GeolocationService` | IP → Nigerian state | `resolve(ip)` → `{country, state, state_code, provider}` (cached) | HTTP client; ip-api.com (default) or ipapi.co; `primary_location_codes` lookup |
| `SettingService` | Typed DB settings, 60-min cache | `get`, `getBool`, `getInt`, `set`, `forget` | `Setting`, Cache |
| `SubmissionWorkflow` | Approval state machine + comment history + notifications | `create`, `approve`, `reject`, `deletableBy` (owner + not approved), `recordComment`, `notifyPendingReviewers`, `notifySubmitter`, `pendingQueryForApprover`, `stageForApprover`, `initialStageForUser`, `nextStageFromStage`. Paths: state `desk_review → zonal_review → hq_review → approved`, directorate `directorate_review → hq_review → approved`, CGIS unit `cgis_desk_review → hq_review → approved` — `hq_admin` approval is final everywhere; `admin_review` is legacy-only (no routing). Writes `application_comments` rows and `user_notifications` on submit/approve/reject/resubmit | `Application`, `ApplicationComment`, `User` |
| `ExcelReportService` | Hand-rolled XLSX writer (ZipArchive, inline strings; no PhpSpreadsheet) | `TEMPLATES` (quarterly/biannual/annual), `periodFor`, `generate` → temp path | `Application` rows |
| `ReportPdfService` | DomPDF downloads | `downloadSubmission` (A4 portrait), `downloadReport` (A4 landscape); NIS logo embedded as base64 | `barryvdh/laravel-dompdf` |
| `Messaging/ResendMailService` | DB-template email | `sendFromTemplate(key, vars, to, name)`; throws if template missing/inactive or `RESEND_API_KEY` unset | resend-php, `Mail::mailer('resend')` |
| `Messaging/TemplateRenderer` | `{{ var }}` substitution | `render(template, vars)` | — |

## 6. Data security

- Passwords: bcrypt (`hashed` cast). Sessions/cache/queue: database-backed.
- `users.mfa_secret` — encrypted cast; `mfa_backup_codes` — encrypted array cast.
- OTPs, magic-link tokens, refresh tokens — stored as SHA-256 hashes only; single-use with TTLs.
- Refresh token rows record `created_ip` and `user_agent`; rotation revokes the predecessor.
- Composite DB indexes on hot paths: `(workflow_stage, scope_code)`, `(workflow_stage, zonal_code)`, `(user_id, is_read, created_at)` notifications, audit `(action, created_at)` etc.

## 7. Frontend stack

- **Build:** Vite 7 + `laravel-vite-plugin`; entries `resources/css/app.css`, `resources/js/app.js`.
- **JS:** Bootstrap 5.3, Chart.js 4, Font Awesome 6, axios. `app.js` = page-typed UI init (sidebar, counters, charts, toasts); `staff-charts.js` (demo data), `dashboard.js`, `chart-setup.js`. No SPA framework — Blade-heavy.
- **CSS:** hand-written (`redas-theme.css`, `styles.css`, `auth-pages.css`, `dashboard.css`, `login.css`). Tailwind 4 is in devDependencies but unused.
- **PWA:** `silviolleite/laravelpwa` manifest (name NIS-REDAS, theme `#003d1a`, standalone, icons + splash under `/images/icons/`, login shortcut) + `public/serviceworker.js` (cache-first precache, `pwa-v{timestamp}` cache names).

## 8. External integrations

| Integration | Use | Config |
|---|---|---|
| Resend (smtp.resend.com:465 + native `resend` mailer) | All transactional email (magic links, OTPs) | `RESEND_API_KEY`, `MAIL_FROM_*` |
| ip-api.com / ipapi.co | IP → state resolution for ABAC | DB settings (`geolocation_provider`, `geolocation_cache_ttl`, `geolocation_fallback_state`, `geolocation_api_token`) |
| Google Authenticator-compatible TOTP | MFA | none (server-side secret) |

Note: ip-api.com default endpoint is plain HTTP — prefer ipapi.co (HTTPS) or a paid ip-api key in production.

## 9. Configuration highlights

`config/app.php`: name `NIS-REDAS`, UTC, AES-256-CBC. `config/database.php`: MySQL default. `cache.php`, `queue.php`, `session.php`: database-backed defaults. `mail.php`: Resend SMTP with native resend transport. `config/laravelpwa.php`: PWA manifest. `config/auth.php` / `sanctum.php`: stock.

## 10. Required environment variables

No `.env.example` ships with the repo (gap). Beyond stock Laravel vars (`APP_KEY`, `APP_URL`, `DB_*`, `CACHE_STORE`, `QUEUE_CONNECTION`, `SESSION_DRIVER`, `MAIL_*`):

| Variable | Default | Used by |
|---|---|---|
| `JWT_SECRET` | **insecure dev fallback** — must be set in production | `JwtService` |
| `JWT_ISSUER` | `nis-redas` | `JwtService` |
| `JWT_ACCESS_TTL` | 900 (s) | `JwtService` |
| `JWT_REFRESH_TTL` | 2592000 (s) | `JwtService` |
| `OTP_TTL_SECONDS` | 300 | API OTP |
| `OTP_MAX_ATTEMPTS` | 5 | API OTP |
| `AUTH_TOKEN_TTL_SECONDS` | 3600 | Magic links |
| `RESEND_API_KEY` | — (required) | Resend mailer |

ABAC/geolocation knobs are DB `settings` (seeded: `abac_enabled=true`, `abac_location_enforcement=true`, `abac_hq_bypass=false`, `abac_allowed_countries=Nigeria`, `geolocation_provider=ip-api`, `geolocation_cache_ttl=1440`, `geolocation_fallback_state=FC`), editable at `/admin/settings` without redeploy.

## 11. Testing

- PHPUnit 11.5; suites: Unit + Feature; env = in-memory SQLite, array cache/session/mail, sync queue, `BCRYPT_ROUNDS=4` (`phpunit.xml`).
- 19 Feature test files (~63 methods): ABAC geolocation, admin settings, audit log, login/registration, desk-admin / directorate / HQ dashboards, middleware, MFA flow, notifications, OTP API, PDF reports, submission workflow. 2 Unit tests.
- Run: `php artisan test` or `vendor/bin/phpunit`.

## 12. Known technical risks

1. `JwtService` falls back to a hardcoded dev secret when `JWT_SECRET` is unset — fail closed instead.
2. Dual API token systems (Sanctum installed but unused vs custom JWT) — consolidate.
3. `otp_login` / `otp_verify_email` email templates referenced but not seeded — runtime mail failure if absent.
4. ~~`user_notifications` has readers, no writers~~ **Resolved** — `SubmissionWorkflow` now produces notifications on submit/approve/reject/resubmit.
5. ip-api.com default is unencrypted HTTP.
6. Stub routes: user report generation, archive upload.
7. Excel writer is a bespoke ZipArchive implementation — zero-dependency but fragile; consider PhpSpreadsheet if complexity grows.
8. ~~`Application::admin()` relation references a non-existent `admin_id` column (dead relation)~~ **Resolved** — the relation was removed and replaced by `Application::reviewComments()` (hasMany `ApplicationComment`).
9. The `2026_08_13` migration for directorate/formation/zone/HQ/CGIS-unit tables is empty — those tables were never created.
10. `users` ↔ location lookups enforced only in application code (no FKs).
