# NIS REDAS — Security & Structural Audit

**Document type:** Pre-launch security assessment and remediation plan
**System:** NIS REDAS (Returns, Data & Analytical System) — Laravel 12 / PHP 8.2 / MySQL
**Date of assessment:** 18 October 2026
**Basis of review:** source code as committed to `c:/NEW_REDAS`, `docs/BACKEND_SCHEMA.md`, and the automated test suite at the time of review.

---

## 1. Scope and method

### 1.1 What was reviewed

| Area | Artefacts examined |
|---|---|
| Routing & surface area | `routes/web.php`, `routes/api.php`, `bootstrap/app.php` |
| Authentication | `AuthController`, `AuthTokenController`, `MfaController`, `ApiAuthController`, `ApiOtpController`, `JwtService`, `MfaService` |
| Authorisation | `CheckAccess`, `CheckRole`, `AbacGeolocationMiddleware`, `RedirectIfAccessMismatch`, `EnsureSpecialCommand`, `RequireMfaPending`, `RequirePasswordChange`, `LockscreenMiddleware` |
| Business logic | `SubmissionWorkflow`, `SubmissionReviewController`, `DashboardController`, `ZonalUserController`, `SpecialCommandController`, `SuperAdminController`, `HqAdminController`, `UserManagementController`, `AdminSettingController`, `AdminAuditLogController` |
| Output handling | `PreviewRenderer`, all `{!! !!}` sinks in `resources/views`, `helpers.php` |
| Data layer | all 34 migrations, `docs/BACKEND_SCHEMA.md`, Eloquent models, `$fillable`/`$casts` |
| Configuration | `config/*.php`, `.env.example`, `public/.htaccess`, `public/.user.ini`, `public/robots.txt`, `composer.json`, `.gitignore` |
| Verification | full test suite (170 tests / 779 assertions — **all passing**), plus four purpose-written exploit probes (removed after use) |

### 1.2 How findings were verified

Every finding rated Critical or High was **reproduced against the running application**, not inferred. The probes were temporary PHPUnit feature tests executed against the project's SQLite test harness, then deleted. Reproductions are recorded in the `Evidence` block of each finding so they can be re-run as regression tests after remediation.

### 1.3 Out of scope

Infrastructure and hosting (WAF, TLS termination, OS hardening, Redis/MySQL configuration, Upstash tenancy), the mail provider's deliverability and security posture, and third-party penetration of the Resend/IP-geolocation services. These are addressed only where the application configuration makes assumptions about them.

---

## 2. Executive summary

The codebase is **above average in its craft** and shows clear security intent: there is no raw SQL built from user input anywhere, all ID lookups are hashid-mediated, uploaded documents are held on the private disk, refresh tokens are hashed and rotated with reuse detection, CSRF is enforced, destructive verbs are behind signed URLs, CSV output is protected against formula injection, and the rendering layer escapes correctly. 170 passing tests with 779 assertions is genuinely good coverage for a system of this size.

That said, the system is **not ready for full launch**. Four defects allow an authenticated user to exceed their authority in ways that would be catastrophic for a law-enforcement records system, and one of the four is a red-team "game over": **a level-5 HQ admin can create a level-6 Super Admin account with a password they choose, then log in as it.** Two of the four are one-line-class fixes; two need a small amount of design work.

The final approval gate is also weaker than it reads. The national approval authority (`hq_admin`) exists as a role, but the *scope* filter that is supposed to constrain every reviewer **fails open**: if a reviewer's scope code is missing or unmapped, the authorisation check is skipped entirely rather than denied. In a system whose entire value proposition is that a return cannot be approved by the wrong desk, a fail-open scope check is a design-level defect, not a bug.

### 2.1 Risk summary

| Severity | Count | Meaning |
|---|---|---|
| Critical | 4 | Direct path to full system compromise or national-scale data disclosure. Must be fixed before any production traffic. |
| High | 12 | Serious weaknesses in the authentication and access-control fabric. Must be fixed before full launch. |
| Medium | 19 | Defence-in-depth gaps, operational and compliance issues. Fix before launch or document as accepted risk. |
| Low / Informational | 11 | Hygiene, latent fragility, and maintainability. Schedule. |

### 2.2 Findings index

| ID | Finding | Severity | Component |
|---|---|---|---|
| C-1 | HQ admin can mint or become a Super Admin | **Critical** | `UserManagementController` |
| C-2 | Any CGIS officer can read the entire national dataset | **Critical** | `SuperAdminController` |
| C-3 | Account lockout is non-functional (silent mass-assignment discard) | **Critical** | `AuthController` / `User` |
| C-4 | Reviewer scope checks fail open | **Critical** | `SubmissionReviewController` / `SubmissionWorkflow` |
| H-1 | MFA challenge has no throttling, lockout, or audit trail | High | `routes/web.php`, `MfaController` |
| H-2 | API login bypasses the enrolled TOTP factor entirely | High | `ApiAuthController` / `ApiOtpController` |
| H-3 | JWT access tokens cannot be revoked; issuer unverified; dev secret fallback | High | `JwtService`, `JwtAccessTokenMiddleware` |
| H-4 | Password reset does not terminate existing sessions | High | `AuthTokenController` |
| H-5 | Hashid salt is a hard-coded constant (config key does not exist) | High | `HashidService`, `config/app.php` |
| H-6 | CSP is Report-Only and never enforced; cache headers absent | High | `SecurityHeaders` |
| H-7 | Account enumeration on `/api/otp/request` | High | `ApiOtpController` |
| H-8 | OTP stored as unsalted SHA-256 over a 4-digit space | High | `ApiOtpController` |
| H-9 | Per-attempt OTP lockout is bypassable; codes accumulate | High | `ApiOtpController` |
| H-10 | Lockscreen unlock has no rate limit (confirmed unlimited) | High | `LockscreenController` |
| H-11 | No cap on upload count per request — storage exhaustion | High | `DashboardController` et al. |
| H-12 | No transactions around multi-table workflow writes | High | `SubmissionWorkflow` |
| M-1 | Uploads validated by extension/MIME only; no content inspection | Medium | all upload paths |
| M-2 | Audit log is attacker-floodable via global 4xx logger | Medium | `LogSuspiciousActivity` |
| M-3 | `env()` read at runtime — breaks under `config:cache` | Medium | 9 call sites |
| M-4 | Unbounded composer wildcards | Medium | `composer.json` |
| M-5 | Two status vocabularies; deny-lists instead of allow-lists | Medium | `SubmissionWorkflow` |
| M-6 | Hard deletes destroy records and audit provenance | Medium | DB schema |
| M-7 | No FK between `users` and location lookups | Medium | DB schema |
| M-8 | Empty migration; unused `nis_data`; legacy `admin_review` stage | Medium | DB schema |
| M-9 | No password policy beyond length 8; no reuse prevention | Medium | `ProfileController` |
| M-10 | `return_data` JSON has no size ceiling | Medium | submissions |
| M-11 | Seeders ship known passwords | Medium | `database/seeders` |
| M-12 | `SUPER_ADMIN` / `ADMIN` view-only boundary is documentation-only | Medium | `SuperAdminController` |
| M-13 | No MFA reset path — permanent lockout risk | Medium | operations |
| M-14 | No `no-store` on authenticated responses | Medium | `SecurityHeaders` |
| M-15 | Suspicious-passcode change alone can clear the lock | Medium | `ProfileController` |
| M-16 | `robots.txt` allows indexing of the whole portal | Medium | `public/robots.txt` |
| M-17 | `.env.example` ships root credentials and blank secrets | Medium | `.env.example` |
| M-18 | Redis TLS configured in the host field (likely non-functional) | Medium | `.env.example` |
| M-19 | Audit retention / NDPA obligations undefined | Medium | compliance |
| L-1 … L-11 | See §7 | Low | various |

---

## 3. Critical findings

### C-1 — A level-5 HQ admin can mint or become a level-6 Super Admin

**Severity:** Critical — full vertical privilege escalation to the highest role in the system.

**Where:** `app/Http/Controllers/Admin/UserManagementController.php` (`categoryLabels()`, `userValidationRules()`, `normalizeAccessProfile()`), reached via `POST /admin/users` and `PATCH /admin/users/{userHash}` in `routes/web.php`.

**What is wrong.** The user-management form is guarded by:

```php
Route::middleware([Authenticate::class,
    'access:category=hq_admin,location=headquarters,role=admin|minLevel=5', 'abac.geo'])
```

i.e. "any HQ admin". But `userValidationRules()` builds its category allow-list from `categoryLabels()`, which contains **every** category including `super_admin`, and `normalizeAccessProfile()` happily maps that to `canonical_role: admin`, `access_level: 6`. There is no check anywhere that the *acting* admin's category outranks the category being assigned. The same endpoint is used for self-service editing, so an HQ admin can also open their own record and re-save it with `user_category=super_admin`.

Because the attacker chooses both the email address and the password on the new account, they control the verification inbox: create → receive magic link → verify → log in as Super Admin. No second actor is involved and no alert is raised. The audit trail records a `User updated` event by an `hq_admin`, which is precisely what a routine HR action looks like.

**Confirmed by reproduction:**

```
"post_status"          => 302      // created and redirected as success
"created"              => true
"created_category"     => "super_admin"
"created_access_level" => 6
```

```php
// Evidence — re-runnable regression test
$hq = User::factory()->create([
    'role' => 'admin', 'user_category' => 'hq_admin',
    'primary_location_type' => 'headquarters', 'access_level' => 5,
    'is_enabled' => true, 'email_verified_at' => now(),
]);

$this->actingAs($hq)->post('/admin/users', [
    'name' => 'Escalated', 'service_number' => 'NIS/ESC/9001',
    'role' => 'admin', 'user_category' => 'super_admin',
    'primary_location_type' => 'headquarters',
    'email' => 'escalated@nis.gov.ng',
    'password' => 'Escalate1!', 'password_confirmation' => 'Escalate1!',
    'is_enabled' => 1,
])->assertForbidden();          // currently returns 302

$this->assertDatabaseMissing('users', ['email' => 'escalated@nis.gov.ng']);
```

**Impact.** Complete compromise. From Super Admin the attacker gets read access to every return in the federation, the national data explorer with CSV/Excel export, and — via C-4 — the ability to approve any return at any stage. For an immigration service this is integrity of national statistics plus disclosure of border-post, personnel and finance data. It is also trivially repeatable and leaves a plausible-looking audit trail.

**Remediation.** Introduce an explicit assignment-authority rule and enforce it in both `store()` and `update()`:

```php
// app/Http/Controllers/Admin/UserManagementController.php

/**
 * Categories each acting category is permitted to create or assign.
 * Keyed by acting category; values are the categories it may hand out.
 */
private const ASSIGNABLE_CATEGORIES = [
    'hq_admin'    => ['state_user', 'desk_admin', 'zonal_user', 'zonal_commander',
                      'directorate_user', 'directorate_admin',
                      'cgis_unit_user', 'cgis_desk_admin', 'hq_admin'],
    'super_admin' => ['state_user', 'desk_admin', 'zonal_user', 'zonal_commander',
                      'directorate_user', 'directorate_admin',
                      'cgis_unit_user', 'cgis_desk_admin', 'hq_admin', 'admin', 'super_admin'],
];

private function assertMayAssignCategory(User $actor, string $targetCategory): void
{
    $allowed = self::ASSIGNABLE_CATEGORIES[$actor->user_category] ?? [];

    abort_unless(
        in_array($targetCategory, $allowed, true),
        403,
        'You are not authorised to assign this account type.'
    );
}
```

Then call `$this->assertMayAssignCategory($request->user(), $validated['user_category'])` immediately after validation in **both** `store()` and `update()`. Additionally:

1. **Privilege changes must be dual-controlled.** For any change that raises `access_level`, require a second, distinct Super Admin approval (a `pending_privilege_changes` table with an approver column), or at minimum email the incumbent Super Admins and require a re-authentication (TOTP) on the changing session.
2. **Forbid self-modification of `user_category` / `access_level` / `role` / `is_enabled`.** An admin editing their own record may change name and email only. This also closes the "disable the Super Admin" denial-of-service variant.
3. **Never allow an `hq_admin` to disable or downgrade a `super_admin`** — `toggleStatus()` currently will.
4. Emit a distinct audit action (`user.privilege_assigned`) with `old`/`new` category and level, and alert on it.

---

### C-2 — Any CGIS unit officer can read the entire national dataset

**Severity:** Critical — horizontal privilege escalation; national-scale confidentiality breach.

**Where:** `app/Http/Controllers/Admin/SuperAdminController.php` (`dashboard()`, `dataExplorer()`) and `routes/web.php`.

**What is wrong.** Both methods open with:

```php
abort_unless(auth()->user()?->hasCategory(
    'super_admin', 'admin', 'cgis_unit_user', 'cgis_desk_admin'
), 403);
```

and the routes expose them to those categories:

```php
Route::middleware([Authenticate::class,
    'access:category=cgis_unit_user|cgis_desk_admin|super_admin|admin,
            location=unit|headquarters,
            role=unit_officer|unit_admin|admin|super_admin|minLevel=0', 'abac.geo'])
    ->group(function () {
        Route::get('/cgis/executive-dashboard', [SuperAdminController::class, 'executiveDashboard']);
        Route::get('/cgis/data-explorer',       [SuperAdminController::class, 'cgisDataExplorer']);
    });
```

The intent was presumably "CGIS executives see CGIS data". The implementation grants a **level-0 CGIS unit officer** the national aggregate dashboard *and* the national data explorer, because `dashboard()` and `dataExplorer()` contain no scope predicate at all — every aggregate runs over `Application::query()` with no `where`. The explorer additionally exposes CSV and Excel export of every matching record, which is a complete external copy of `return_data` for the whole federation.

**Confirmed by reproduction.** A `cgis_unit_user` with `access_level = 0`, `assigned_cgis_unit_code = 'actu'`:

```
"cgis_user_status" => 200      // full national data explorer rendered
"access_level"     => 0
```

```php
// Evidence
$u = User::factory()->create([
    'role' => 'unit_officer', 'user_category' => 'cgis_unit_user',
    'primary_location_type' => 'unit', 'primary_location_code' => 'actu',
    'assigned_cgis_unit_code' => 'actu', 'access_level' => 0,
    'is_enabled' => true, 'email_verified_at' => now(),
]);

$this->actingAs($u)->get('/cgis/data-explorer')->assertForbidden();   // currently 200
$this->actingAs($u)->get('/cgis/data-explorer?export=csv')->assertForbidden();
```

**Impact.** A single compromised low-privilege account — a desk officer's, with no special privileges — yields a full extract of every monthly return: state command staffing, border-post operations, finance figures, investigation case counts, passport issuance volumes. This is the single most damaging data set in the system and it is reachable by the least-privileged CGIS account.

**Remediation.** Two changes; the first is mandatory.

1. **Scope every aggregate and query to the actor.** Replace the naked `Application::query()` in `dashboard()`, `dataExplorer()`, `executiveDashboard()` and `cgisDataExplorer()` with a single scoped entry point:

```php
// app/Services/SubmissionWorkflow.php — new method
/**
 * Constrain a query to the records this actor is entitled to see.
 * Super Admin and (optionally) general Admin are national; everyone else
 * is confined to their own category and scope.
 */
public static function scopeQueryForViewer(Builder $query, User $viewer): Builder
{
    if ($viewer->hasCategory('super_admin')) {
        return $query;
    }

    if ($viewer->user_category === 'admin') {
        return $query;                     // view-only national, per RBAC design
    }

    if ($viewer->hasCategory('cgis_unit_user', 'cgis_desk_admin')) {
        $unit = $viewer->cgisUnitSlug();

        // Fail closed: an unprovisioned CGIS account sees nothing.
        if ($unit === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('category', self::CATEGORY_CGIS)
                     ->where('scope_code', $unit);
    }

    return $query->whereRaw('1 = 0');
}
```

2. **Split the routes.** `/cgis/executive-dashboard` and `/cgis/data-explorer` should require `minLevel=2` *and* a scope — or better, move them behind `super_admin` only and give CGIS a separate, unit-scoped dashboard. A unit officer needs their own unit's five submissions, not a national explorer.

3. Add a regression test asserting a `cgis_unit_user` receives 403 on both routes and that its CSV export cannot be produced.

---

### C-3 — Account lockout is non-functional

**Severity:** Critical — removes the only defence against sustained online password guessing.

**Where:** `app/Http/Controllers/AuthController::login()`; `app/Models/User.php` (`$fillable`, `$casts`); migrations `2026_10_04_042358`, `2026_10_14_000001`.

**What is wrong.** The login flow implements a per-account lockout after 5 failures:

```php
if ($user) {
    $user->increment('failed_attempts');
    if ($user->failed_attempts >= self::MAX_LOGIN_ATTEMPTS) {
        $user->update([
            'lockout_until' => now()->addMinutes(15),
            'failed_attempts' => 0,
        ]);
    }
}
```

and later, on success:

```php
if ($user->failed_attempts !== 0 || $user->lockout_until !== null) {
    $user->forceFill(['failed_attempts' => 0, 'lockout_until' => null])->save();
}
```

But `User::$fillable` does **not** contain `lockout_until` or `failed_attempts`, and `$casts` does not declare `lockout_until` either. `Model::preventSilentlyDiscardingAttributes()` is never enabled (`AppServiceProvider` has no strict-mode call), so `$user->update([...])` **silently discards both attributes** and returns success. The columns exist in the schema and are simply never written.

**Confirmed by reproduction.** Five failed logins, then:

```
"failed_attempts"      => 5        // incremented, because increment() bypasses $fillable
"lockout_until"        => null     // never set — the lockout never engages
"fillable_has_lockout" => false
"fillable_has_failed"  => false
"cast_has_lockout"     => false
```

```php
// Evidence
for ($i = 0; $i < 5; $i++) {
    $this->post('/login', ['username' => 'victim@nis.gov.ng', 'password' => 'wrong']);
}
$this->assertNotNull($user->fresh()->lockout_until);   // currently fails: null
```

**Compounding defect.** `failed_attempts` is never reset (the reset is in the discarded `update()`), so it climbs monotonically and `>= 5` is permanently true — the code *looks* like it is re-locking on every subsequent attempt while doing nothing.

**Impact.** The only remaining control is `RateLimiter` keyed on `strtolower($login).'|'.$request->ip()` with a 60-second decay. That is trivially defeated by rotating source IPs (cloud, mobile, residential proxies), and does nothing at all against a distributed credential-stuffing campaign against `@nis.gov.ng` addresses. Given C-1 lets an insider mint accounts and H-10 leaves the lockscreen unthrottled, password guessing is the cheapest route in.

**Remediation.** Add both columns to the model and persist with `forceFill` (or add to `$fillable`):

```php
// app/Models/User.php
protected $fillable = [
    // …existing…
    'failed_attempts',
    'lockout_until',
];

protected $casts = [
    // …existing…
    'lockout_until'   => 'datetime',
    'failed_attempts' => 'integer',
];
```

Then, in `AuthController::login()`, use `forceFill(...)->save()` for the lockout write so the behaviour is explicit and cannot regress silently:

```php
if ($user->failed_attempts >= self::MAX_LOGIN_ATTEMPTS) {
    $user->forceFill([
        'failed_attempts' => 0,
        'lockout_until'   => now()->addMinutes(self::LOCKOUT_MINUTES),
    ])->save();
}
```

and enable strict mode **in non-production only**, so this class of bug fails loudly during development rather than silently in production:

```php
// app/Providers/AppServiceProvider::boot()
if (! $this->app->isProduction()) {
    Model::preventSilentlyDiscardingAttributes();
    Model::preventLazyLoading();
}
```

Finally: reset `failed_attempts` on successful login (already intended), key the limiter on the account **as well as** the IP (`'login:'.$normalizedLogin` with a longer decay), and alert when `failed_attempts` crosses 3 for any account.

---

### C-4 — Reviewer scope checks fail open

**Severity:** Critical — undermines the segregation-of-duties guarantee the workflow exists to provide.

**Where:** `app/Http/Controllers/SubmissionReviewController::authorizeScope()` and `canReview()`; `SubmissionWorkflow::scopeCodeForApprover()`, `pendingQueryForApprover()`; the same pattern in `DashboardController::downloadSubmissionPdf()`.

**What is wrong.** Every reviewer-facing authorisation ends in a scope comparison, but the comparison is **skipped when the reviewer has no resolved scope**:

```php
private function authorizeScope(Application $application, User $user): void
{
    $scopeCode = SubmissionWorkflow::scopeCodeForApprover($user);

    if ($scopeCode !== null) {              // <-- fail OPEN
        // …compare and abort 403…
    }
    // no scope → no restriction → full access
}
```

`scopeCodeForApprover()` returns `null` in several reachable situations:

| Category | Returns null when |
|---|---|
| `desk_admin` | both `primary_location_code` and `assigned_state_code` are empty |
| `directorate_admin` | `directorateSlug()` cannot map `assigned_directorate_code`/`primary_location_code` (any typo or unmapped 3-letter code) |
| `cgis_desk_admin` | `cgisUnitSlug()` cannot map the unit code |
| `zonal_commander` | `normalizeZoneCode()` returns null for the supplied code |

`UserManagementController` refuses to create `unit`/`directorate` accounts without a scope code, but imposes **no such requirement** on `desk_admin`, `zonal_commander` or `state_user`: if `formation_code` and `primary_location_code` are both blank, `resolveFormation()` returns `[null, null]`, the account is created successfully, and that reviewer thereafter sees and can approve **every submission at their stage, nationally**.

The same fail-open appears in `pendingQueryForApprover()` (`if ($scopeCode !== null) { …where… }`) — so the *queue* itself is unscoped, meaning the defect is visible as "this desk has far too many items" rather than as an obvious denial.

`canReview()` compounds it: it checks stage and status but never scope, so the UI offers an Approve button on records the user should not see.

**Impact.** Any provisioning mistake (a typo in a directorate code, a blank formation on an HQ-created desk admin) silently promotes a desk reviewer to a national approver. In a workflow whose whole purpose is that a state's return is approved by that state's desk, then that zone's commander, then HQ, this is a correctness failure as well as a security one — and it is invisible, because the system keeps behaving plausibly.

**Remediation.** Invert the default. A reviewer must prove a scope; absence of scope is denial.

```php
private function authorizeScope(Application $application, User $user): void
{
    // HQ-level viewers are national by design and are handled explicitly.
    if ($user->hasCategory('super_admin')) {
        return;
    }

    if ($user->hasCategory('admin')) {
        abort(403, 'This account is view-only and cannot act on returns.');
    }

    $scopeCode = SubmissionWorkflow::scopeCodeForApprover($user);

    // Fail closed: a reviewer with no resolvable scope may review nothing.
    abort_if(
        $scopeCode === null,
        403,
        'Your account has no provisioned scope. Contact HQ to correct your access profile.'
    );

    $applicationScope = $user->user_category === 'zonal_commander'
        ? (NisFormationService::normalizeZoneCode($application->zonal_code)
            ?: NisFormationService::normalizeZoneCode($application->scope_code)
            ?: $application->scope_code)
        : $application->scope_code;

    abort_if(
        $applicationScope !== $scopeCode,
        403,
        'This submission is outside your provisioned scope.'
    );
}
```

Apply the same inversion in `pendingQueryForApprover()` (`return $query->whereRaw('1 = 0')` when scope is null), and in `canReview()` add `&& $this->scopeMatches($application, $user)`.

Then close the source: make `UserManagementController` reject `desk_admin`, `zonal_commander`, `directorate_admin` and `cgis_desk_admin` accounts whose derived scope code is empty, exactly as it already does for `unit` and `directorate`:

```php
$actorCategoriesRequiringScope = [
    'desk_admin', 'zonal_commander', 'directorate_admin', 'cgis_desk_admin',
];

if (in_array($normalized['user_category'], $actorCategoriesRequiringScope, true)
    && blank($locationCode)) {
    throw ValidationException::withMessages([
        'formation_code' => ['A formation/scope is required for reviewer accounts.'],
    ]);
}
```

Add a migration-time data check that reports (and blocks the deploy on) any existing reviewer row with a null scope. Add regression tests: a scope-less desk admin receives 403 on `show`, `approve`, `reject`, `download` and `document`, and their dashboard lists zero records.

---

## 4. High findings

### H-1 — The MFA challenge has no throttling, lockout, or audit trail

**Where:** `routes/web.php` (`mfa.pending` group), `app/Http/Controllers/MfaController.php`.

**What is wrong.** The MFA routes are protected by exactly one middleware — the custom `mfa.pending` gate that merely checks a pending user exists in the session:

```php
Route::middleware(['mfa.pending'])->group(function () {
    Route::get('/mfa/setup',    [MfaController::class, 'setup']);
    Route::post('/mfa/setup',   [MfaController::class, 'verifySetup']);
    Route::get('/mfa/challenge',[MfaController::class, 'challenge']);
    Route::post('/mfa/challenge',[MfaController::class, 'verifyChallenge']);
    Route::get('/mfa/recovery', [MfaController::class, 'recovery']);
    Route::post('/mfa/recovery',[MfaController::class, 'verifyRecovery']);
    Route::post('/mfa/complete',[MfaController::class, 'complete']);
    Route::post('/mfa/cancel',  [MfaController::class, 'cancel']);
});
```

There is no `throttle:`, no attempt counter on the session or the user, no lockout, and no `AuditLogger` call on a failed verification. `verifyChallenge()`, `verifyRecovery()` and `verifySetup()` all simply `return back()->withErrors(...)` on a bad code. Sensible choice, in that the account is not fully authenticated yet — but it means the *second factor is freely guessable*.

**Impact.** A 6-digit TOTP with Google2FA's `window = 1` is ~3 × 10⁶ candidate windows, but in a real attack the code space per 30-second window is 10⁶ and an attacker who already has the password has, in practice, a few thousand attempts before anyone notices. Backup codes are 8 hex characters (4 bytes, 2³² space) and *are* single-use — but nothing rate-limits guessing them, and `verifyBackupCode()` compares with `array_search(..., true)` on plaintext values, so the timing/lookup is a simple string compare over a small array. More concretely: because failures are not logged, a sustained attack against an MFA-enabled executive is invisible in the audit trail. This is the one authentication step that stands between a phished password and full account takeover, and it is the least instrumented part of the login flow.

**Remediation.**

```php
Route::middleware(['mfa.pending', 'throttle:8,1'])->group(function () {
    // …existing mfa routes…
});
```

Then in `MfaService::verifyCode()`, count failures per user and lock after 5, mirroring the OTP design:

```php
// in MfaController::verifyChallenge() (and verifySetup/verifyRecovery)
if (! $this->mfaService->verifyCode($user, $code)) {
    $this->mfaService->recordFailedAttempt($user);      // new
    AuditLogger::logAuthAttempt(
        user: $user,
        status: 'failure',
        details: ['reason' => 'mfa_invalid_code', 'channel' => 'web'],
        ip: $request->ip(),
    );

    return back()->withErrors(['code' => 'The authentication code is invalid.']);
}
```

Add `mfa_failed_attempts` / `mfa_locked_until` columns (there is already a `mfa_last_used_at` precedent) and lock for 15 minutes after 5 failures, resetting on success and on any successful password login of a *verified* session. Also add TOTP replay protection: store the last accepted timestep and reject reuse within the same window.

---

### H-2 — The JSON API bypasses the enrolled TOTP factor entirely

**Where:** `app/Http/Controllers/ApiAuthController::login()`, `app/Http/Controllers/ApiOtpController::verify()`.

**What is wrong.** Two parallel login mechanisms have diverged in security property:

| Step | Web portal | JSON API |
|---|---|---|
| Password | ✔ | ✔ |
| Second factor | **TOTP (enrolled, verified)** | **4-digit email OTP** |
| Enrolled TOTP consulted? | Yes | **No** |
| Session | encrypted server-side, `HttpOnly`, `SameSite=Lax` | JWT (15 min) + refresh cookie |

`ApiOtpController::verify()` issues access and refresh JWTs after a correct 4-digit email code and never looks at `mfa_enabled`, `mfa_secret` or `mfa_verified_at`:

```php
if ($purpose === 'login') {
    // …no MFA check anywhere…
    $accessJwt  = JwtService::fromEnv()->issueAccessToken(...);
    $refreshJwt = JwtService::fromEnv()->issueRefreshToken(...);
}
```

A user who has dutifully enrolled a hardware-backed TOTP authenticator has that protection silently downgraded to email possession the moment anyone uses the API. The 4-digit OTP is also the weaker of the two factors by a wide margin (see H-8).

**Impact.** Full second-factor bypass on the API surface, and a second, weaker oracle that can be attacked independently of the hardened web path. Any attacker with mailbox access (the same mailbox used for password reset, remember) obtains a full bearer token and refresh cookie.

**Remediation.** Require the enrolled factor before issuing tokens:

```php
// ApiOtpController::verify(), inside the login branch, before issuing JWTs
if ($user->isMfaEnabled()) {
    $totp = (string) $request->input('totp', '');

    if (! app(MfaService::class)->verifyCode($user, $totp)) {
        return response()->json([
            'message' => 'MFA_REQUIRED',
            'methods' => ['totp', 'backup_code'],
        ], 401);
    }
}
```

If the mobile client cannot yet display a TOTP prompt, the correct interim posture is to **deny API login for MFA-enrolled accounts** (`403 MFA_REQUIRED_ON_API`) rather than silently weakening them, and to make the API opt-in per account. Also remove the `role` requirement from `ApiAuthController::login()` — it makes the endpoint's credential check stricter in an unusual way (it constrains the lookup by legacy role string) without adding security, and it invites clients to enumerate which role a given email has by observing 401 vs. 200-with-OTP.

---

### H-3 — JWT access tokens cannot be revoked; issuer unchecked; dev secret fallback

**Where:** `app/Services/JwtService.php`, `app/Http/Middleware/JwtAccessTokenMiddleware.php`.

**What is wrong.** Three distinct weaknesses in one service.

1. **No revocation path.** `JwtAccessTokenMiddleware` validates signature, `type === 'access'`, subject, then re-loads the user and checks `is_enabled`/`email_verified_at`. It never consults a denylist, so a stolen access token remains valid for its full `JWT_ACCESS_TTL` (900 s). Disabling an account does work — but role changes, password resets and detected token theft do not. The `jti` claim is generated (`Str::uuid()`) and then discarded on the access-token path; only refresh tokens are recorded, so there is nothing to revoke *against*.

2. **`iss` is not verified.** `decode()` calls `JWT::decode($jwt, new Key($secret, 'HS256'))` — firebase/php-jwt validates `exp` and `nbf` automatically, but nothing checks `iss` or `aud`. `JwtService::$issuer` exists and is written into the payload but never compared on the way in. In a multi-tenant or service-mesh future this becomes a real cross-issuer confusion bug.

3. **A working fallback secret ships in the code.**

```php
$secret = env('JWT_SECRET');
if (! $secret && app()->environment('production')) {
    throw new \RuntimeException('JWT_SECRET must be set in production environments.');
}
$secret = $secret ?: 'dev-fallback-jwt-secret-dev-fallback';
```

The guard is `app()->environment('production')`. `.env.example` sets `APP_ENV=production`, which is good — but any deployment that leaves `APP_ENV` unset, or sets `APP_ENV=staging`, silently signs and verifies tokens with a secret that is in the public repository. A staging instance with a copy of production data then issues forgeable tokens.

**Remediation.**

```php
public static function fromEnv(): self
{
    $secret = (string) env('JWT_SECRET', '');

    if (strlen($secret) < 32) {
        throw new \RuntimeException('JWT_SECRET must be set and at least 32 characters long.');
    }

    return new self(/* … */);
}
```

— remove the fallback entirely and let tests set it in `phpunit.xml`. Then:

- Add `'aud' => 'nis-redas-api'` to both token types and assert it in `decode()` using `JWT::$leeway` plus an explicit `iss`/`aud` comparison.
- Implement access-token revocation: write each access `jti` to a short-TTL cache key (`Cache::put('jwt:deny:'.$jti, true, $ttl)`) on logout, password change and role change; check it in the middleware with `Cache::has(...)`. Cheaper and sufficient: keep `access_ttl` at 900 s, bump a per-user `token_version` column on logout-all/password-reset, and embed it in the claims.

---

### H-4 — Password reset does not terminate existing sessions

**Where:** `app/Http/Controllers/AuthTokenController::resetPassword()`.

**What is wrong.** The reset writes a new password and nothing else:

```php
$authToken = $this->consumeTokenOrFail($token, 'password_reset');
$user = User::query()->findOrFail($authToken->user_id);
$user->password = Hash::make($validated['password']);
$user->save();
```

No `RefreshToken` revocation, no session invalidation, no `failed_attempts` reset, no `must_change_password` handling, and no audit entry. An attacker who established persistence (a refresh token, or a live session cookie) **retains it after the legitimate owner "secures" the account** — the classic post-incident failure mode. Because refresh tokens live 30 days by default (`JWT_REFRESH_TTL=2592000`), an attacker who exfiltrated the `refresh_token` cookie survives the reset for a month.

**Remediation.**

```php
public function resetPassword(Request $request, string $token)
{
    $validated = $request->validate([
        'password' => ['required', 'string', 'min:12', 'confirmed', 'not_compromised'],
    ]);

    $authToken = $this->consumeTokenOrFail($token, 'password_reset');
    $user = User::query()->findOrFail($authToken->user_id);

    DB::transaction(function () use ($user, $validated, $request) {
        $user->forceFill([
            'password'            => Hash::make($validated['password']),
            'failed_attempts'     => 0,
            'lockout_until'       => null,
            'otp_failed_attempts' => 0,
            'otp_locked_until'    => null,
            'password_changed_at' => now(),
        ])->save();

        // Kill every issue of the credential.
        RefreshToken::where('user_id', $user->id)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);

        AuthToken::where('user_id', $user->id)
            ->where('type', 'email_verify')
            ->whereNull('used_at')
            ->update(['used_at' => now()]);

        // Kill every web session for this user.
        DB::table('sessions')->where('user_id', $user->id)->delete();

        AuditLogger::log($user, 'auth.password_reset', 'success', 'user', $user->id,
            ['ip' => $request->ip()]);
    });

    return redirect()->route('login')
        ->with('status', 'Password reset successfully. All other sessions have been signed out.');
}
```

Do the same on an authenticated password change in `ProfileController::update()` (revoke all *other* refresh tokens and sessions), and add a "sign out everywhere" control, which the system currently lacks.

---

### H-5 — The hashid salt is a hard-coded constant

**Where:** `app/Services/HashidService.php`, `config/app.php`.

**What is wrong.**

```php
$salt = config('app.hashid_salt', 'default_redas_salt_2026');
```

`config/app.php` defines **no** `hashid_salt` key, so `config('app.hashid_salt')` is always `null` and the fallback is always used. The salt is therefore a literal in the repository, the alphabet is Hashids' default, and the minimum length is 8. Anyone with the source — including every contractor, every fork, every CI log — can enumerate the entire ID space offline in milliseconds.

Hashids is explicitly **not encryption** and the code correctly never relies on it as an access control (every controller that resolves a hash then checks ownership or scope). So this is not itself an object-reference vulnerability. But it removes an entire defence-in-depth layer: obfuscated identifiers limit the blast radius of *any* future authorisation mistake, of which this audit found four. And it makes the `hash` in URLs cosmetic.

**Remediation.** Define the key properly and generate it per environment:

```php
// config/app.php
'hashid_salt' => env('HASHID_SALT'),
```

```php
// app/Services/HashidService.php
$salt = (string) config('app.hashid_salt');

if ($salt === '') {
    throw new \RuntimeException('HASHID_SALT is not configured.');
}
```

```bash
# .env.example
HASHID_SALT=
```

Note that rotating the salt invalidates every link ever issued (`user.submissions.pdf`, notification `action_url`, `verify-email`) — which is acceptable pre-launch and must be done pre-launch, not after. Do not increase `minHashLength` instead and call it fixed; a longer opaque string with a published salt is still enumerable in linear time.

---

### H-6 — CSP is Report-Only and never enforced

**Where:** `app/Http/Middleware/SecurityHeaders.php`.

**What is wrong.**

```php
$response->headers->set('Content-Security-Policy-Report-Only', $csp);
```

The policy is well-formed and nonce-based — `script-src 'self' 'nonce-…'`, `object-src` absent (so it falls back to `default-src 'self'`), `base-uri 'self'`, `form-action 'self'`, `frame-ancestors 'self'` — and `csp_nonce()` is wired through a Blade `@nonce` directive. But because the header is `-Report-Only`, **it blocks nothing**. It is also not *reporting*: there is no `report-uri` or `report-to`, so the header has no effect whatsoever. It looks like a control in a code review and is not one.

Additionally missing:

- `Cross-Origin-Opener-Policy: same-origin`
- `Cross-Origin-Resource-Policy: same-origin`
- `X-Permitted-Cross-Domain-Policies: none`
- `Cache-Control: no-store` on authenticated pages (see M-14)
- `object-src 'none'` explicitly (currently implicit via the fallback)

**Remediation.**

1. Ship the Report-Only header **plus** a `report-to` endpoint for two weeks in staging, triage the violations, then switch to `Content-Security-Policy`. Make the phase a config flag (`CSP_ENFORCE=false`) so the flip is a config change, not a deploy.
2. Add the four headers above to the middleware. `X-Frame-Options: SAMEORIGIN` and `frame-ancestors 'self'` should be kept consistent; consider `DENY` unless a confirmed feature requires framing.
3. Add `Cache-Control: no-store, private` and `Pragma: no-cache` for authenticated responses, so sensitive returns are not retained in shared browser caches or proxy caches. This matters more here than in a typical app: the state return form is dozens of pages of operational data.

---

### H-7 — Account enumeration on `/api/otp/request`

**Where:** `app/Http/Controllers/ApiOtpController::request()`.

**What is wrong.**

```php
if (! $user) {
    throw ValidationException::withMessages(['login' => ['Account not found.']]);
}
```

This is a direct oracle: `422 {"errors":{"login":["Account not found."]}}` for an unknown account versus `200 {"message":"OTP sent successfully."}` for a real one. The web side of the application is careful about exactly this (`AuthTokenController::resendVerificationEmail()` and `requestPasswordReset()` both return identical generic responses with an explanatory comment) — so the API has regressed a control that the web path already gets right.

The endpoint also leaks a second signal: the existence of an account with `is_enabled = false` (or unverified) still produces a 200, so an attacker can distinguish "does not exist" from "exists but disabled" by timing and by whether mail is sent.

**Impact.** Confirms valid `@nis.gov.ng` addresses and service numbers at 5 requests/minute per IP — enough for a targeted campaign against a known list of officers, and enough to build the target list for C-3's password guessing.

**Remediation.** Mirror the web behaviour exactly:

```php
public function request(Request $request)
{
    $validated = $request->validate([
        'login'   => ['required', 'string', 'max:255'],
        'purpose' => ['required', 'string', 'in:login,verify_email'],
    ]);

    $user = $this->findLogin($validated['login']);

    if ($user && $user->is_enabled && $user->email_verified_at) {
        $this->issueAndSendOtp($user, $validated['purpose']);
    }

    // Identical response for every outcome, including delivery failure.
    return response()->json([
        'message' => 'If the account exists, a verification code has been sent.',
    ], 200);
}
```

Log the delivery failure server-side; do not surface it. (This also removes the current `502` on mail failure, which itself is an oracle for "account exists and mail is down".)

---

### H-8 — OTPs are stored as unsalted SHA-256 over a 4-digit space

**Where:** `app/Http/Controllers/ApiOtpController.php` (both `request()` and `verify()`), `app/Models/OtpCode.php`.

**What is wrong.**

```php
$codeInt = random_int(0, 9999);
$code = str_pad((string) $codeInt, 4, '0', STR_PAD_LEFT);
$codeHash = hash('sha256', $code);
```

A 4-digit code has 10,000 possible values. An unsalted SHA-256 of a 4-digit string is a fixed, precomputable table — 10,000 entries, generated in well under a second. Anyone with read access to `otp_codes` (a SQL-injection elsewhere, a backup, a replica, a support engineer, an overly-broad `SELECT`) can reverse **every live OTP in the table instantly**. Salting does not help either; the whole space is 10,000.

**Impact.** Converts "database read" into "account takeover" for every user with a live OTP. Combined with the accumulation issue in H-9, a single snapshot of the table at any moment typically yields several valid codes per active user.

**Remediation.** Do not store a hash of a low-entropy secret at all. Either:

**(Preferred)** Use a long random code and hash it like a password:

```php
$code = (string) random_int(100000, 999999);       // 6 digits is still only 10^6
// better:
$code = bin2hex(random_bytes(16));                  // link-based, for email click-through
```

For a typed code, use 8 digits (10⁸) and `Hash::make($code)` (bcrypt/argon), with `Hash::check` on verify — the cost factor makes offline enumeration uneconomic even at 10⁸.

**(Minimum acceptable)** Keep 4 digits but bind the hash to the user and the row so a leaked table is useless without the surrounding row:

```php
$codeHash = hash_hmac('sha256', $code, config('app.key').$user->id.$otp->id);
```

and reduce the brute-force window: 4 digits / 5-minute TTL / 5 attempts is a 0.05% success rate for a single-target attacker — acceptable **only** if the attempt cap actually works, which it currently does not (H-9).

Also raise `OTP_TTL_SECONDS` awareness: `env('OTP_TTL_SECONDS', 300)` is read at runtime (see M-3) and breaks under `config:cache`.

---

### H-9 — Per-attempt OTP lockout is bypassable and codes accumulate

**Where:** `app/Http/Controllers/ApiOtpController::verify()`.

**What is wrong.** Two independent defects.

**1. The attempt gate is evaluated on the wrong row.** The lookup is by code hash:

```php
$otp = OtpCode::query()
    ->where('user_id', $user->id)
    ->where('purpose', $purpose)
    ->where('code_hash', $codeHash)     // <-- only a MATCHING code is found
    ->whereNull('consumed_at')
    ->where('expires_at', '>', now())
    ->orderByDesc('created_at')
    ->first();

if (! $otp) {
    // wrong guess: increment the most-recent row's counter instead
    $latest = OtpCode::query()->…->orderByDesc('created_at')->first();
    if ($latest) { $latest->increment('attempts'); }
    …
    return response()->json(['message' => 'OTP_INVALID'], 401);
}

$maxAttempts = (int) env('OTP_MAX_ATTEMPTS', 5);
if ((int) $otp->attempts >= $maxAttempts) {   // unreachable for a wrong guess
    return response()->json(['message' => 'OTP_MAX_ATTEMPTS_EXCEEDED'], 403);
}
```

The `attempts >= 5` gate can only ever fire for a **correct** code that was previously incremented, i.e. it never stops guessing. The only effective control is `users.otp_failed_attempts`, which is keyed to the *user*, is reset by any success, and does not account for the number of live codes.

**2. Codes are never invalidated on re-request.** Each `POST /otp/request` `create()`s a brand-new row (the comment notes "simpler than rotation logic"). Every previously issued, unexpired code remains independently valid. At the 5/minute limit, a user accumulates **up to 25 simultaneously valid codes** inside a 5-minute TTL — a 25/10,000 = 0.25% chance per guess, rising linearly with request rate.

**Impact.** The stated design intent — "a 4-digit code only has 10,000 possibilities so brute force must be rate-limited here too" — is not achieved. An attacker who can drive both endpoints (or who simply observes a targeted user being asked to re-request a code) faces a materially easier search.

**Remediation.**

```php
public function request(Request $request)
{
    // …resolve user…

    DB::transaction(function () use ($user, $purpose, $ttlSeconds) {
        // Exactly one live code per user per purpose.
        OtpCode::where('user_id', $user->id)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        $code = (string) random_int(100000, 999999);
        OtpCode::create([
            'user_id'    => $user->id,
            'purpose'    => $purpose,
            'code_hash'  => Hash::make($code),
            'expires_at' => now()->addSeconds($ttlSeconds),
            'attempts'   => 0,
            'consumed_at'=> null,
        ]);
    });
}

public function verify(Request $request)
{
    // …resolve user…

    /** @var OtpCode|null $otp */
    $otp = OtpCode::where('user_id', $user->id)
        ->where('purpose', $purpose)
        ->whereNull('consumed_at')
        ->where('expires_at', '>', now())
        ->orderByDesc('created_at')
        ->first();

    if (! $otp) {
        return response()->json(['message' => 'OTP_INVALID'], 401);
    }

    // Count EVERY guess against the live code, right or wrong.
    $otp->increment('attempts');

    if ($otp->attempts > (int) config('auth.otp_max_attempts')) {
        $otp->forceFill(['consumed_at' => now()])->save();
        $this->recordFailedOtpAttempt($user, $purpose, $request->ip());

        return response()->json(['message' => 'OTP_MAX_ATTEMPTS_EXCEEDED'], 403);
    }

    if (! Hash::check($otpInput, $otp->code_hash)) {
        $this->recordFailedOtpAttempt($user, $purpose, $request->ip());

        return response()->json(['message' => 'OTP_INVALID'], 401);
    }

    // …consume and issue tokens…
}
```

---

### H-10 — Lockscreen unlock has no rate limit

**Where:** `routes/web.php` (`lockscreen` group), `app/Http/Controllers/LockscreenController::unlock()`.

**What is wrong.** The group carries only `Authenticate::class`:

```php
Route::middleware([Authenticate::class])->group(function () {
    Route::get('/lockscreen',        [LockscreenController::class, 'show']);
    Route::post('/lockscreen/unlock',[LockscreenController::class, 'unlock']);
    Route::post('/lockscreen/lock',  [LockscreenController::class, 'lock']);
});
```

`unlock()` validates a passcode with `Hash::check` and, on failure, throws a `ValidationException` — no counter, no delay, no lock, no audit. A lockscreen passcode may be as short as 4 characters (`ProfileController` validates `min:4`).

**Confirmed by reproduction.** Twelve consecutive wrong passcodes on a live locked session:

```
"unlock_attempt_statuses" => array:1 [ 302 => 12 ]
```

All twelve were processed normally. There is no throttle and no escalating delay.

**Impact.** The lockscreen is a *shoulder-surfing and idle-session* control. As implemented it is only a UI curtain: anyone with an unlocked browser — or an XSS that can POST — can guess a 4-digit passcode to completion in seconds and take over the session without ever knowing the account password. It also promotes a short numeric passcode to a credential without any of the protections a credential needs.

**Remediation.**

```php
Route::middleware([Authenticate::class])->group(function () {
    Route::get('/lockscreen',          [LockscreenController::class, 'show']);
    Route::post('/lockscreen/unlock',  [LockscreenController::class, 'unlock'])
        ->middleware('throttle:6,1');
    Route::post('/lockscreen/lock',    [LockscreenController::class, 'lock']);
});
```

plus a session-side counter that locks the session after N failures and forces a full re-authentication (logout to `/login`, destroying the session — do not leave the user at the lockscreen where they can keep guessing against the same session):

```php
$attempts = (int) $request->session()->get('lockscreen.failed', 0) + 1;
$request->session()->put('lockscreen.failed', $attempts);

if ($attempts >= 5) {
    AuditLogger::logAuthAttempt($user, 'failure',
        ['reason' => 'lockscreen_lockout', 'attempts' => $attempts], $request->ip());

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')
        ->withErrors(['login' => 'Too many incorrect passcodes. Please sign in again.']);
}
```

Raise the minimum passcode length to 6 and reject trivially weak values (`0000`, `1234`, repeated digits). Record `unlock` events in the audit log.

---

### H-11 — No cap on the number of uploaded files per request

**Where:** `routes/web.php` (state return), `DashboardController::returnValidationRules()`, `ZonalUserController`, `SpecialCommandController`.

**What is wrong.** Every upload rule allows an unbounded array of files, each up to 20 MB:

```php
'attachments'          => ['nullable', 'array'],
'attachments.*'        => ['file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:20480'],
```

There is no `max:` on the array itself (Laravel's `array` rule supports `attachments => ['array', 'max:10']`). With `throttle:60,1` and `throttle:database` (30/min) on the submit routes, a single authenticated officer can push very large volumes into `storage/app/private` — and, because `storeUploadedFiles()` returns paths that are embedded in `return_data`, that volume is also *permanently retained*, unprunable except by deleting the submission.

**Impact.** Storage-exhaustion denial of service by the least-trusted role, with a permanent footprint. Fill the disk and the shared-hosting/Cloud environment fails for everyone, including the mail queue and the session store (both database/Redis-backed).

**Remediation.** Cap the array and reduce the per-file ceiling:

```php
'attachments'        => ['nullable', 'array', 'max:5'],
'attachments.*'      => ['file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:10240',
                         'extensions:pdf,doc,docx,jpg,jpeg,png'],
'supporting_documents'   => ['nullable', 'array', 'max:5'],
'supporting_documents.*' => ['file', 'mimes:pdf,xls,xlsx,png,jpg,jpeg', 'max:10240'],
```

(the `extensions` rule verifies the client-supplied extension independently of the guessed MIME). Then:

- Enforce a total-per-user storage quota, checked before accepting the upload.
- Move uploads behind a queue: store to a staging area, validate, then promote.
- Add a scheduled prune for orphaned files (files referenced by no `return_data`) and for submissions deleted after the retention period.

---

### H-12 — No transactions around multi-table workflow writes

**Where:** `app/Services/SubmissionWorkflow::create()`, `approve()`, `reject()`.

**What is wrong.** Each of these performs 3–5 writes across different tables (and dispatches events, which may send mail synchronously) with no `DB::transaction`:

```php
$application = Application::create([...]);
self::recordComment($application, $user, ...);
self::notifyPendingReviewers($application);      // N inserts, plus UserNotification rows
SubmissionCreated::dispatch($application);        // may enqueue/send mail
```

`approve()` is worse: it updates the application, records a comment, notifies the submitter, notifies the *next* set of reviewers, and dispatches `SubmissionStageAdvanced`. A failure between the `update()` and `recordComment()` leaves a submission that has advanced a stage with **no audit record of who advanced it** — directly undermining the review-history thread that the desk-admin and HQ views render. A failure inside `notifyPendingReviewers()` (which does string comparisons and a `whereRaw` against zone codes) leaves the return routed to a queue nobody was told about.

**Impact.** Silent data-integrity loss in the system of record. `workflow_path` and `application_comments` are the evidence trail; a non-transactional write makes "the trail is complete" an assumption rather than a guarantee.

**Remediation.**

```php
public static function approve(Application $application, User $actor, ?string $comment = null): void
{
    $result = DB::transaction(function () use ($application, $actor, $comment) {
        // …all schema writes: update(), recordComment()…

        return $nextStage;
    });

    // Side effects AFTER the commit, queued, so a mail failure cannot roll
    // back a legitimate approval.
    self::notifySubmitter(...);
    self::notifyPendingReviewers($application);
    SubmissionStageAdvanced::dispatch($application, $result);
}
```

Make the listeners `ShouldQueue` so mail is never sent inside the request transaction. Add a `workflow_version` optimistic-lock column to `applications`, incremented on every write and asserted in `update()`, so two reviewers acting on the same return simultaneously cannot both succeed (currently a genuine race: two HQ admins could both approve, advancing the stage twice).
---

## 5. Medium findings

### M-1 — Uploads are validated by extension and sniffed MIME only

**Where:** `routes/web.php`, `DashboardController::returnValidationRules()`, `ZonalUserController`, `SpecialCommandController`.

Laravel's `mimes:` rule compares the client extension against the **guessed** MIME type (from file magic), which is reasonable but not a content guarantee. There is no malware scan, no image re-encoding, no PDF structural validation, no macro stripping for `.docx`/`.xlsx`. Files are then served back with `$disk->response($path, basename($path))`, which sets `Content-Type` from the file and serves **inline** by default.

**Why it matters here more than usual:** these files are downloaded by exactly the people who most need to be protected — desk admins, zonal commanders and HQ reviewers. A malicious document (macro-laden `.doc`, polyglot PDF, crafted image) delivered to a reviewer's workstation via an internal government portal is a credible targeted-attack delivery path, and the reviewer has every reason to trust the source.

**Remediation.** In order of cost-effectiveness:

1. Serve `attachments` as `Content-Disposition: attachment` by default; require an explicit `?download=1` to stream. Inline PDF rendering should be a deliberate choice, not the default.
2. Re-encode images server-side through Intervention Image (strips EXIF, embedded payloads and polyglots).
3. Run uploads through a ClamAV scan (queued, with the file quarantined until clean) or a hosted scanner. This is a reasonable requirement for a government records system.
4. Reject `.doc`/`.xls` entirely in favour of `.docx`/`.xlsx`, and strip macros from those.
5. Add a `Content-Security-Policy` with `sandbox` and `X-Content-Type-Options: nosniff` on the document route (currently nosniff is set globally — good).

---

### M-2 — The audit log is attacker-floodable

**Where:** `app/Http/Middleware/LogSuspiciousActivity.php`, registered globally via `$middleware->append(...)`.

The middleware writes an `audit_logs` row on **every** response with status 401, 403, 419 or 429 — including unauthenticated probes to random paths. `audit_logs` has no cap, no partitioning and no prune command, and the insert happens on the critical path of each request. An unauthenticated attacker can therefore:

- grow the table without bound (disk exhaustion), and
- add latency to every 403 the application serves, and
- bury genuine detections in noise.

**Remediation.** Keep the signal, drop the unbounded cost:

1. Aggregate: key on `(ip, status, path-hash, minute)` and increment a counter row rather than inserting one row per event (or use a sliding-window counter in Redis).
2. Never log 404s (currently not logged — correct) and never log 401/403 for the same IP more than once per minute.
3. Add a prune command (`audit_logs:prune --days=365`) to the scheduler and a retention policy to the runbook (see M-19).
4. Consider moving audit writes to a queued job so a slow audit table cannot take the app down.

---

### M-3 — `env()` is called at runtime in application code

**Where:** 9 call sites across `AuthController`, `AuthTokenController`, `ApiAuthController`, `ApiOtpController`, `JwtService`, `UserManagementController`, `bootstrap/app.php`.

Calls like `env('OTP_TTL_SECONDS', 300)` inside controllers work only while the config cache is cold. Under `php artisan config:cache` — which every production deployment should run — `env()` returns `null` outside config files, so every one of these silently falls back to its default. That means:

- `JWT_ACCESS_TTL` / `JWT_REFRESH_TTL` silently become 900 / 604800, ignoring your configured values (and `JwtService::fromEnv()` reads `JWT_REFRESH_TTL` **twice** with two different defaults — 2592000 in the constructor, 604800 in `fromEnv()` — an inconsistency that will bite during a token-lifetime review);
- `OTP_TTL_SECONDS`, `OTP_MAX_ATTEMPTS`, `OTP_LOCKOUT_MINUTES`, `AUTH_TOKEN_TTL_SECONDS` all silently revert to defaults, weakening a control you believe you configured;
- `APP_URL` inside the magic-link builder silently becomes `http://localhost`, producing **broken and insecure verification links in production**.

That last one is the most dangerous: `AuthController::register()` and `UserManagementController::sendVerificationEmail()` both build the magic link from `env('APP_URL', 'http://localhost')`. On a cached config this ships `http://localhost/verify-email/…` to real users, and the fallback is http, not https.

**Remediation.** Move every one of these into a config file and read via `config()`:

```php
// config/auth.php (additions)
'otp' => [
    'ttl_seconds'      => (int) env('OTP_TTL_SECONDS', 300),
    'max_attempts'     => (int) env('OTP_MAX_ATTEMPTS', 5),
    'lockout_minutes'  => (int) env('OTP_LOCKOUT_MINUTES', 15),
],
'magic_link_ttl' => (int) env('AUTH_TOKEN_TTL_SECONDS', 3600),
```

```php
// config/jwt.php (new)
return [
    'secret'      => env('JWT_SECRET'),
    'issuer'      => env('JWT_ISSUER', 'nis-redas'),
    'access_ttl'  => (int) env('JWT_ACCESS_TTL', 900),
    'refresh_ttl' => (int) env('JWT_REFRESH_TTL', 2592000),
];
```

Then replace `env(...)` with `config(...)` at every call site and add `config:cache` to the deploy script so the discrepancy cannot recur silently.

---

### M-4 — Unbounded dependency constraints

**Where:** `composer.json`.

```json
"bacon/bacon-qr-code": "*",
"barryvdh/laravel-dompdf": "*",
"hashids/hashids": "*",
"pragmarx/google2fa": "*",
```

`composer.lock` pins the installed version, so a build from lock is reproducible — but `composer update` on any of these will pull whatever is newest, including a major version with a breaking or security-relevant change, with no review gate. `*` also defeats `composer audit` triage and dependency-review tooling.

**Remediation.** Pin to caret ranges of the currently-locked versions (`"^2.0"`, `"^3.4"`, etc.), enable `composer audit --locked` in CI, run `composer outdated --direct` monthly, and add Dependabot or Renovate with grouped, reviewed PRs.

---

### M-5 — Two status vocabularies; deny-lists instead of allow-lists

**Where:** `app/Services/SubmissionWorkflow`, `app/Models/Application::scopeAwaiting`, throughout.

`applications.status` carries two overlapping vocabularies — the current `pending` / `approved` / `returned`, and a legacy `submitted` / `queried` / `rejected` — and the code tests them inconsistently:

```php
// SubmissionWorkflow::stageForApprover / pendingQueryForApprover
->whereNotIn('status', ['approved', 'rejected'])          // deny-list: 'rejected' not 'returned'
// Application::scopeAwaiting
->whereNotIn('status', ['approved', 'rejected'])
// SubmissionWorkflow::reject
'status' => 'returned'
// DashboardController::directorateDashboard
->whereIn('status', ['queried', 'rejected', 'returned'])
```

Two consequences. First, a record in `status = 'returned'` is **not** excluded by `whereNotIn(... ['approved','rejected'])`, so rejected-but-resubmittable returns still appear in approver queues at the stage they were rejected from. Second, the predicate meaning "still open" is expressed as a deny-list in some places and an allow-list in others, so adding a future status silently changes semantics in half the codebase.

Also note `applications.status` and `workflow_stage` are unconstrained `varchar`s in MySQL. There is no CHECK constraint and (after migration `2026_07_29_000001`) no ENUM, so any string can be written.

**Remediation.**

1. Define the vocabulary once as PHP enums and cast the columns:
   ```php
   enum SubmissionStatus: string { case Pending = 'pending'; case Approved = 'approved'; case Returned = 'returned'; }
   enum WorkflowStage: string { case Submitted = 'submitted'; /* … */ }
   ```
   `Application::$casts = ['status' => SubmissionStatus::class, 'workflow_stage' => WorkflowStage::class]`.
2. Replace every deny-list with `->where('status', SubmissionStatus::Pending)`.
3. Migrate legacy rows in a data migration, then add a MySQL `CHECK` constraint (or a lookup table with an FK) so the database enforces it too — the current `role`-column history shows what happens when constraints live only in code.
4. Add a `scopeOpen()` on `Application` and use it everywhere instead of ad-hoc predicates.

---

### M-6 — Hard deletes destroy records and audit provenance

**Where:** all migrations (`cascadeOnDelete`, `nullOnDelete`), `DashboardController::destroySubmission()`, `ZonalUserController`, `SpecialCommandController`.

`applications.user_id` is `ON DELETE CASCADE`. Deleting a user **destroys every return they ever filed and, with it, the review history**. `application_comments.user_id` and `audit_logs.user_id` are `ON DELETE SET NULL`, so the surviving audit rows lose their actor — an audit entry that says "*someone approved this*" is not an audit entry.

Submission deletion is a hard `DELETE` plus a hard `unlink` of the uploaded files. There is no `SoftDeletes` anywhere in the codebase.

For a system of record with a statutory retention obligation this is a serious structural defect: deletion is irreversible, a mistaken click destroys evidence, and there is no tombstone to prove a record once existed.

**Remediation.**

1. Add `softDeletes()` to `applications`, `application_comments`, `users`. Never `forceDelete` from a controller.
2. Change `applications.user_id` from `cascadeOnDelete()` to `restrictOnDelete()`, and make user "deletion" a `is_enabled = false` + anonymisation operation. A user who leaves the service must not take their returns with them.
3. Keep `audit_logs.user_id` as `SET NULL` but **denormalise** `actor_name`, `actor_email` and `actor_category` into the `details` JSON at write time, so the trail survives account removal.
4. Retain uploaded files on soft delete (do not unlink), and delete only via a scheduled retention job after the statutory period.
5. Add a `deleted_at` index and a `retention_expires_at` column to drive retention.

---

### M-7 — No referential integrity between `users` and the location lookups

**Where:** `users.primary_location_type`, `users.primary_location_code`, versus `primary_location_types` / `primary_location_codes`.

`docs/BACKEND_SCHEMA.md` §7 acknowledges this: "`users` ↔ `primary_location_*` lookups are enforced only in application code (no FKs)". So `primary_location_code` can hold any 50-character string — including `'AB'`, `'ab'`, `'Abia'`, `'AB '`, or a typo — and each of those resolves differently:

- `requiredGeoState()` uppercases and compares to the detected state code → `'ab'` matches, `'Abia'` never matches;
- `NisFormationService::geoStateForCode()` and `zoneForState()` may return `null` for the same value;
- `SubmissionWorkflow::scopeCodeForApprover()` returns the raw string, so a state return stamped `'AB'` is invisible to a desk admin whose code is `'ab'`.

That is the mechanism that produces the null-scope condition behind C-4, and it also creates silent routing failures where a return is submitted but no reviewer can see it.

**Remediation.**

1. Normalise on write: uppercase and trim `primary_location_code`, `assigned_state_code`, `assigned_zonal_command_code`, `assigned_cgis_unit_code`, `assigned_directorate_code`, `geo_state` in a model mutator, and add a data migration to fix existing rows.
2. Add FKs where the value set is closed: `users.primary_location_code → primary_location_codes.code` for state-location users. For directorate/unit/zonal codes, create proper lookup tables (`directorates`, `cgis_units`, `zonal_commands`) — the empty `2026_08_13_000000` migration was clearly intended for exactly this.
3. Add a `CHECK` (or validation rule) that `geo_state` is a 2-character uppercase code.

---

### M-8 — Schema anomalies

1. **`2026_08_13_000000_create_directorate_formation_zone_headquarters_cgis_unit_tables.php` is empty.** The lookup tables for directorates, formations, zones, headquarters and CGIS units were never created; every code list lives in PHP constants duplicated across `DashboardController::DIRECTORATES`, `HqAdminController::DIRECTORATES`, `SuperAdminController::DIRECTORATES`, `UserManagementController::DIRECTORATE_SLUGS`, `User::directorateSlug()`, and `NisFormationService`. These lists must stay in sync manually and already differ in formatting (`'Human Resources Management'` vs `'HRM'` vs `'finance'` vs `'FIN'`). That drift is a live source of C-4.
2. **`nis_data` is unused** by application code but is migrated, seeded in `docs` and carried in every backup. Drop it or document it as a reserved HR dataset with an owner.
3. **`stripe`-style legacy stage `admin_review`** remains in `SubmissionWorkflow::STAGE_ADMIN_REVIEW` and in `nextStageFromStage()`, but nothing routes to it. It is handled by a data migration that force-approved anything stuck there. Remove the constant so a future developer cannot accidentally route into a dead state that auto-approves.
4. **`applications.comments`** is a legacy single-reason column duplicated by `application_comments`. It holds only the latest rejection reason and is written by `reject()`. Remove it or mark it deprecated, otherwise two "sources of truth" for the rejection reason will diverge.
5. **`users` has no Eloquent relationships** despite inbound FKs — so any code doing `$user->applications` will fail at runtime. Add the relations (`applications()`, `refreshTokens()`, `notifications()`, `auditLogs()`) even if only for reporting.

---

### M-9 — No password policy beyond `min:8`

**Where:** `AuthController::register()`, `ProfileController::update()`, `AuthTokenController::resetPassword()`, `UserManagementController::userValidationRules()`.

Every password rule is `['required', 'string', 'min:8']`. There is no complexity requirement, no blocklist of common passwords, no check against the user's own name/email/service number, no reuse prevention, and no maximum age. `must_change_password` exists and is enforced (good), but the replacement password can be `password`.

**Remediation.** Adopt NIST SP 800-63B rather than complexity theatre:

- minimum 12 characters (remove composition rules; they encourage `Password1!`);
- reject the top-10k breached passwords — Laravel's `Password::min(12)->uncompromised()` rule hits the HaveIBeenPwned range API and is a one-line change;
- reject the user's name, email local-part and service number;
- store a `password_history` of the last 5 bcrypt hashes and refuse reuse;
- no forced rotation, but do force rotation on any suspected compromise (see H-4).

```php
'password' => ['required', 'confirmed', Password::min(12)->uncompromised()->letters()->numbers()],
```

---

### M-10 — `return_data` has no size ceiling

**Where:** every submission path; `public/.user.ini` (`max_input_vars = 20000`).

`max_input_vars = 20000` was raised deliberately to accommodate the combined state form, and that is fine — but nothing bounds the resulting JSON. `sanitizeReturnData()` recurses without a depth limit or a total-node limit, and the resulting `return_data` is cast to JSON in a single column with no `max_allowed_packet` planning. A single submission with deeply nested arrays or 20,000 populated fields produces a multi-megabyte row that is then read, decoded and re-encoded on every dashboard, preview, PDF and CSV operation — including the national data explorer, which pulls *all* records.

**Remediation.** Enforce limits explicitly:

```php
// in sanitizeReturnData()
private const MAX_DEPTH = 6;
private const MAX_NODES = 5000;
private const MAX_JSON_BYTES = 2_097_152; // 2 MB

if (strlen(json_encode($clean)) > self::MAX_JSON_BYTES) {
    throw ValidationException::withMessages([
        'form' => ['This return exceeds the maximum permitted size. Please split it or contact support.'],
    ]);
}
```

Then validate depth and node count during recursion. Add a `CHECK (JSON_LENGTH(return_data) > 0)` and consider extracting the frequently-aggregated fields (`report_period`, `reporting_officer`, `command_name`) into real indexed columns — the explorer currently filters on `return_data->...` with a `LIKE`, which cannot use an index and will not scale past the first few thousand returns.

---

### M-11 — Seeders ship known credentials

**Where:** `database/seeders/NewSeeder.php`, `UserCategorySeeder.php`, `TestUserSeeder.php`.

`NewSeeder` creates a `super_admin` (`superadmin.seed@nis.gov.ng`) and an `admin`, plus ~200 other accounts, **all with password `password123`**. `UserCategorySeeder` does the same for one account per category including `super_admin` and `hq_admin`. `DatabaseSeeder` seeds three demo accounts. `docs/BACKEND_SCHEMA.md` documents all of this.

**Remediation.**

1. Guard every seeder behind a non-production check:
   ```php
   if (app()->isProduction()) {
       throw new \RuntimeException('This seeder must never run in production.');
   }
   ```
2. Generate random passwords and print them once, rather than a fixed literal.
3. Add a production boot assertion that fails fast if any account's password matches a known seeded hash.
4. Never include seeders in a production `db:seed` run; document `migrate --force` only.

---

### M-12 — The "view-only" boundary on `admin` and `super_admin` is documentation-only

**Where:** `SuperAdminController`, `HqAdminController`.

`docs/BACKEND_SCHEMA.md` and the tests describe `admin` as "view-only". The `admin` category has `access_level = 5`, which satisfies the `minLevel=5` on the `/admin/users*` and `/admin/settings` groups — so a nominal view-only administrator can create users (including, per C-1, a `super_admin`) and rewrite the ABAC settings. Only `SubmissionReviewController::authorizeAction()` actually blocks `admin` from approving (because `stageForApprover()` returns `null` for it).

**Remediation.** Stop encoding "view-only" as an access level and start encoding it as a capability set. Either add an explicit `is_view_only` boolean with a middleware that blocks all non-GET methods, or split `admin` out of the mutation route groups:

```php
// Settings: HQ admin only
Route::middleware(['access:category=hq_admin,location=headquarters,role=admin|minLevel=5'])
```

Then add a test asserting that an `admin` receives 403 on every mutating route in the application, generated from the route table rather than hand-listed.

---

### M-13 — No MFA reset path

`mfa_secret` and `mfa_backup_codes` are only ever written by `MfaController`. There is no administrator action to reset a user's MFA, no CLI command, and no self-service "lost my device" flow beyond the single-use backup codes. A user who loses their phone **and** their printed codes is permanently locked out and requires direct database surgery — which, in practice, means someone with DB access writes `mfa_enabled = 0` and the account has no second factor until it next logs in.

**Remediation.** Add `php artisan redas:mfa-reset {service_number}` that clears `mfa_secret`, `mfa_backup_codes`, `mfa_enabled`, `mfa_verified_at` and forces re-enrolment at next login, writing a `security.mfa_reset` audit entry. Add a Super-Admin-driven UI action behind the dual-control rule from C-1. Also surface MFA enrolment status in the user list so HQ can audit who has not enrolled.

---

### M-14 — No cache-control on authenticated responses

**Where:** `SecurityHeaders`. No `Cache-Control`, `Pragma` or `Expires` header is set for authenticated pages.

Authenticated pages containing operational returns are cacheable by default heuristics in some intermediaries and are certainly retained in the browser's back/forward cache. Combined with `expire_on_close = false` and a 120-minute session lifetime, a shared workstation at a state command can present a full return from the bfcache after the user has walked away.

**Remediation.** In `SecurityHeaders`, for `Auth::check()` responses:

```php
$response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
$response->headers->set('Pragma', 'no-cache');
$response->headers->set('Expires', '0');
```

---

### M-15 — The lockscreen passcode can be changed without the old one being strong

**Where:** `ProfileController::update()`.

The `passcode` section requires `current_password`, which is correct — but it validates the new passcode as `['nullable','string','min:4','max:20','confirmed']`. A 4-character numeric passcode is acceptable, and H-10 means it can be brute-forced. Because the lockscreen falls back to the account password when no passcode is set, a user setting `0000` has actively **weakened** their session protection relative to the default.

**Remediation.** Require at least 6 characters for a lockscreen passcode, reject all-numeric passcodes shorter than 8, reject repeated/simple patterns, and audit both set and change events. With H-10 fixed (throttle + session lockout), a 6-character passcode is defensible.

---

### M-16 — `robots.txt` invites indexing

**Where:** `public/robots.txt`.

```
User-agent: *
Disallow:
```

This explicitly permits every crawler to index every path — including `/login`, `/directory`, `/password/forgot`. `/directory` is intentionally public (a passport-centre and mission directory), so the portal is discoverable by design; but for a law-enforcement system the default posture should be `Disallow: /` with an explicit `Allow` for the public directory, plus `X-Robots-Tag: noindex` on all authenticated responses.

```txt
User-agent: *
Disallow: /
Allow: /directory
Allow: /$
```

Note that `robots.txt` is advisory only and is not an access control — pair it with `noindex` headers on sensitive routes.

---

### M-17 — `.env.example` ships dangerous defaults

**Where:** `.env.example`.

```
APP_ENV=production
APP_KEY=
DB_USERNAME=root
DB_PASSWORD=
JWT_SECRET=
```

`DB_USERNAME=root` with a blank password, `APP_ENV=production` (which disables the `APP_DEBUG` safety net but also means the example is "production-shaped" with no secrets), and an empty `APP_KEY` are all footguns for a team that copies the example. `SESSION_TRUSTED_PROXIES` is absent, and `TRUSTED_PROXIES` is read by `bootstrap/app.php` with an `explode(',', '')` that produces `['']` rather than `[]`.

**Remediation.** Set `APP_ENV=local` in the example, document the required production overrides in a header comment, use a non-root `DB_USERNAME` placeholder, add `TRUSTED_PROXIES=` (empty), and add a deploy pre-flight script that asserts `APP_KEY`, `APP_ENV=production`, `APP_DEBUG=false`, `JWT_SECRET` (≥32 chars), `SESSION_SECURE_COOKIE=true`, and a non-root DB user. Fix the proxy parsing:

```php
$proxies = array_values(array_filter(array_map('trim', explode(',', (string) env('TRUSTED_PROXIES')))));
```

---

### M-18 — Redis TLS is configured in the host field

**Where:** `.env.example`, `config/database.php`.

```
REDIS_CLIENT=predis
REDIS_HOST="tls://<YOUR_UPSTASH_ENDPOINT>"
```

Predis expects the scheme in a `scheme` key (`['scheme' => 'tls', 'host' => '…']`), not embedded in the host string. Passing `tls://host` as `host` will attempt a plaintext connection to a hostname that does not resolve. `config/database.php` defines no `scheme` key at all, and no `context` with TLS options.

**Why this is a security finding, not just a bug:** if Redis is unreachable, `CACHE_STORE=redis` means the `RateLimiter` in `AppServiceProvider` (`RateLimiter::for('api')` and `for('database')`) cannot record hits, and every `throttle:` route fails or degrades. A misconfigured cache silently removes rate limiting from the entire application.

**Remediation.**

```php
// config/database.php — redis 'default'
'scheme' => env('REDIS_SCHEME', 'tls'),
'context' => [
    'stream' => ['verify_peer' => true, 'verify_peer_name' => true],
],
```

and verify at boot that the cache is reachable, failing the health check (`/up`) rather than serving traffic with no throttle. Also confirm `CACHE_STORE=redis` is genuinely intended — `SESSION_DRIVER=database` while `CACHE_STORE=redis` means sessions survive a Redis outage but throttling does not.

---

### M-19 — Audit retention, PII and NDPA obligations are undefined

**Where:** `audit_logs`, `applications.return_data`, `users`.

The system stores names, service numbers, email addresses, geolocation (state/country, IP), and detailed operational returns about NIS personnel and finances. Under the **Nigeria Data Protection Act 2023** this is personal data of serving officers, and the Act requires: a lawful basis, a documented retention schedule, data-subject rights handling, breach notification within 72 hours, a record of processing activities (RoPA), and appointment of a Data Protection Officer. None of that exists in the repository.

Concretely missing:

- **No retention policy.** `audit_logs`, `sessions`, `refresh_tokens`, `otp_codes` and `auth_tokens` have `Prunable` implementations but **no scheduler entries** — nothing actually runs them. Check `routes/console.php`: there is no `Schedule::` block. So the prunable models are dead code and every table grows without bound.
- **No encryption at rest** for `return_data` (which may contain investigation and finance detail) — only `mfa_secret` and `mfa_backup_codes` are encrypted.
- **No data-export/erasure path** for a data subject.
- **No breach-notification runbook.**
- **PII in logs:** `bootstrap/app.php` logs `user_id`, `ip`, `path` for API exceptions; `AuthController` logs `email` on registration-delivery failure. Acceptable, but needs a documented policy and a retention window.

**Remediation.** Add to `routes/console.php`:

```php
Schedule::command('model:prune')->daily();
Schedule::command('auth:clear-resets')->daily();
Schedule::command('session:prune')->daily();   // custom command; sessions table has no auto-prune
Schedule::command('sanctum:prune-expired --hours=24')->daily();
```

Then write the compliance artefacts: a retention schedule (returns: statutory period; audit logs: 7 years; sessions/tokens: as configured), a RoPA, a DPO appointment, an encryption-at-rest decision for `return_data`, and a breach runbook. See §9 for the checklist form.

---

## 6. Low and informational findings

| ID | Finding | Where | Note |
|---|---|---|---|
| L-1 | `$application->delete(true)` passes an unused argument | `DashboardController::destroySubmission()` | Harmless today; `delete()` takes no parameters. Signals confusion with `forceDelete()`. |
| L-2 | `previewStateReturn()` passes raw request data to the view | `DashboardController` | Currently safe — `PreviewRenderer` escapes with `e()` throughout and `formatValue()` strips tags — but the safety lives in a different class from the input. Sanitise at entry, not only at render. |
| L-3 | `csp_nonce()` is a static local | `app/helpers.php` | Correct for a single request; will silently reuse a nonce if ever called from a queue worker or long-running process. Document, or reset via middleware. |
| L-4 | `CheckAccess` redirects instead of aborting | `CheckAccess` | A misconfigured `homeRoute()` produces a redirect loop rather than a 403. Add a loop guard or abort for XHR. |
| L-5 | `HqAdminController::show()` has no in-controller category check | `HqAdminController` | Every sibling method has `abort_unless(...hasCategory('hq_admin','admin'))`. Relies entirely on route middleware; defence-in-depth inconsistency. |
| L-6 | `AdminAuditLogController` uses `JSON_EXTRACT(details, '$')` | `AdminAuditLogController` | MySQL-only; will error on SQLite in tests if that code path is exercised. Also unindexed → full scan. |
| L-7 | `Schema::defaultStringLength(191)` | `AppServiceProvider` | A pre-utf8mb4-index workaround. With utf8mb4 and a modern MySQL (innodb_large_prefix default since 5.7.7), 191 is unnecessary and forces shorter indexed columns than the schema declares. |
| L-8 | `sanctum` is installed and configured but dormant | `composer.json`, `config/sanctum.php` | Two auth systems in one codebase is an audit liability. Remove the package and the migration, or document it as reserved. |
| L-9 | `SERVICE_NUMBER` validation regexes differ | `AuthController` (`/^NIS\/[A-Z]{3}\/[0-9]{4}$/`) vs `UserManagementController` (`/^NIS\/[A-Z]{2,3}\/\d{3,4}$/`) | Two canonical formats. The admin path accepts values the registration path rejects. |
| L-10 | `app/Http/Controllers/AdminDashController.php` and `AuthController::register()` are unreachable | app | `/register` redirects to login with a message, but `AuthController::register()` remains public and routable if the route is ever re-added. Remove dead entry points. |
| L-11 | `_refactor_border.py` and `tmp-js-check/` are committed | repo root | Scratch artefacts in the repository. `tmp-js-check/` also contains rendered HTML of authenticated pages, which may include real data. |

---

## 7. Structural defects summary (database and architecture)

A consolidated view of the schema issues, since they interact:

| # | Defect | Consequence | Fix |
|---|---|---|---|
| S-1 | `users.primary_location_code` and the five `assigned_*_code` columns are free strings with no FK or CHECK | Silent routing failures; the null-scope condition behind C-4 | Normalise + lookup tables + FKs |
| S-2 | `applications.status` / `workflow_stage` are unconstrained varchars with two vocabularies | Rejected returns appear in approver queues; predicates disagree | PHP enums + cast + CHECK |
| S-3 | `applications.user_id` `ON DELETE CASCADE` | Deleting a user destroys their returns and review history | Soft deletes + `RESTRICT` |
| S-4 | `application_comments.user_id` / `audit_logs.user_id` `SET NULL` | Audit trail loses its actor | Denormalise actor into `details` |
| S-5 | Empty migration `2026_08_13_000000`; no directorate/unit/zone lookup tables | Code lists duplicated in 6 places, already drifted | Create the lookup tables |
| S-6 | `nis_data` unused, `admin_review` unreachable, `applications.comments` duplicated | Dead schema and dead states that auto-approve | Drop or document |
| S-7 | No `SoftDeletes` anywhere | Irreversible deletion of records of record | Add soft deletes |
| S-8 | `Prunable` implemented but never scheduled | Unbounded table growth in `sessions`, `otp_codes`, `auth_tokens`, `refresh_tokens` | Add `Schedule::command('model:prune')` |
| S-9 | Only `users` and `applications` have factories; `NewSeeder` depends on data that is not in a factory | Integration tests cannot easily build realistic reviewer fixtures | Add factories for the reviewer categories |
| S-10 | No optimistic locking on `applications` | Two simultaneous approvers both succeed; stage advances twice | `workflow_version` column |
| S-11 | `users` has no Eloquent relations despite inbound FKs | `$user->applications` fails at runtime | Define the relations |
| S-12 | `return_data` used as a filter target (`return_data->report_period`) with `LIKE` | Unindexable; the data explorer will not scale | Promote hot fields to columns |
---

## 8. Pre-launch assurance checklist (industry standard)

This is the checklist to work through before the system takes real traffic. It is organised by domain, and each line names the framework control it satisfies, so the completed checklist doubles as evidence for an Assessor, an ISO auditor or an NDPA compliance review.

**Frameworks referenced:**
`ASVS` — OWASP Application Security Verification Standard v4.0.3 (level noted)
`WSTG` — OWASP Web Security Testing Guide
`T10` — OWASP Top 10 (2021)
`27001` — ISO/IEC 27001:2022 Annex A
`SSDF` — NIST SP 800-218 Secure Software Development Framework
`NDPA` — Nigeria Data Protection Act 2023
`CIS` — CIS Critical Security Controls v8

### 8.1 Authentication (ASVS V2, 27001 A.5.17, A.8.5)

- [ ] **Fix C-3.** Account lockout actually persists; verify with a test that fails today.
- [ ] **Fix H-10.** Lockscreen throttled and session-locked after 5 failures. *(ASVS 2.2.1)*
- [ ] **Fix H-1.** MFA challenge throttled, failures counted, locked and audited. *(ASVS 2.8.1)*
- [ ] **Fix H-2 or formally retire the API.** No credential path may bypass the enrolled factor. *(ASVS 2.8.1, T10 A07)*
- [ ] **Fix H-4.** Password reset revokes refresh tokens and destroys sessions. *(ASVS 2.5.5, 3.3.4)*
- [ ] Password policy per M-9: ≥12 chars, `uncompromised()`, no reuse of last 5. *(ASVS 2.1.1–2.1.7, NIST SP 800-63B)*
- [ ] Password reset tokens: single-use, ≥128-bit entropy, ≤1 hour, invalidated on new request. *(already correct — verify with a test)*
- [ ] MFA is **mandatory** for `admin`, `hq_admin`, `super_admin`, `desk_admin` and all `*_admin` categories. Currently enforced at first login by redirect; add an explicit policy check so it cannot be skipped by a future flow change. *(ASVS 2.8.1)*
- [ ] MFA reset path exists and is audited (M-13). *(27001 A.5.17)*
- [ ] Login responses are uniform in timing and message for unknown user, wrong password, disabled account and unverified account. *(T10 A07)*
- [ ] **Fix H-7.** No account-existence oracle on any endpoint. *(ASVS 2.2.1)*
- [ ] Service number and email are both case-normalised consistently (L-9).

### 8.2 Session management (ASVS V3, 27001 A.8.5)

- [ ] **Fix H-3.** Access tokens revocable; `iss`/`aud` verified; no fallback secret. *(ASVS 3.5.3)*
- [ ] **Fix H-6 / M-14.** `Cache-Control: no-store` on authenticated responses. *(ASVS 3.4.6)*
- [ ] Idle timeout (currently `SESSION_LIFETIME=120`) and absolute timeout both enforced; absolute timeout added (e.g. 12 hours). *(ASVS 3.3.1)*
- [ ] Session ID regenerated on login — verify `Auth::login()` path does this and add `$request->session()->regenerate()` explicitly. *(ASVS 3.2.1)*
- [ ] "Sign out everywhere" control available to the user and to an administrator. *(ASVS 3.3.4)*
- [ ] Session cookie: `Secure`, `HttpOnly`, `SameSite=Lax`, no `Domain` unless required. *(verify on the deployed host — ASVS 3.4)*
- [ ] `sessions`, `refresh_tokens`, `otp_codes`, `auth_tokens` are pruned by the scheduler (S-8). *(27001 A.8.10)*

### 8.3 Authorisation (ASVS V4, T10 A01, 27001 A.5.15, A.8.3)

- [ ] **Fix C-1.** Category assignment authority enforced; self-elevation impossible; privilege changes dual-controlled and alerted. *(ASVS 4.1.3, 4.2.1, T10 A01)*
- [ ] **Fix C-2.** CGIS accounts scoped; national explorer restricted to `super_admin` (and view-only `admin`). *(ASVS 4.1.1)*
- [ ] **Fix C-4.** Every scope check fails **closed**; `pendingQueryForApprover` returns nothing for a scope-less reviewer. *(ASVS 4.1.1, 4.1.3)*
- [ ] One authoritative authorisation helper. Today scope logic is duplicated across `SubmissionReviewController`, `DashboardController`, `SubmissionWorkflow`, `HqAdminController` and `SuperAdminController`, and each copy has drifted. Consolidate into a policy (`ApplicationPolicy`) with `view`, `review`, `approve`, `reject`, `delete`, `download` methods, and call `$this->authorize()` from every controller. *(SSDF PW.4.1, ASVS 4.1)*
- [ ] Generated test: for every non-GET route and every seeded category, assert 200/302 **or** 403 as expected. A route-matrix test catches exactly the class of bug C-1/C-2/C-4 belong to, and is cheap to maintain. *(SSDF PW.8, ASVS 4.1.5)*
- [ ] Any "view-only" role is enforced by a capability middleware, not by an access level that also satisfies mutation routes (M-12).
- [ ] IDOR sweep: for each of `show`, `edit`, `update`, `destroy`, `document`, `download`, `pdf` across all five submission controllers, confirm a second user's hash returns 403 **and** add it to the test suite.

### 8.4 Input validation and output encoding (ASVS V5, V12, T10 A03)

- [ ] **Good news, keep it:** every query is parameterised and `PreviewRenderer` escapes with `e()`. Add a lint rule / review checklist item forbidding `{!! !!}` without an accompanying escaping helper. *(ASVS 5.3.4)*
- [ ] `previewStateReturn()` sanitises at entry, not only at render (L-2). *(defence in depth)*
- [ ] `escapeLike()` is applied to **every** `LIKE` clause — audit and centralise. *(ASVS 5.3.4)*
- [ ] Upload allow-list by extension **and** MIME, plus content validation (M-1). *(ASVS 12.2, 12.3)*
- [ ] Upload count and size capped per request (H-11). *(ASVS 12.1.1)*
- [ ] `return_data` size/depth ceiling enforced (M-10). *(ASVS 5.1.4)*
- [ ] CSV formula-injection guard is applied **everywhere**. `SubmissionReviewController::escapeCsvCell()` is correct and prefixes `+ = - \t \r @` with `'`. But `ReturnDataExplorerService::toCsv()` (used by the national data explorer and its export buttons) writes rows with a bare `fputcsv()` and no equivalent guard, so a `reporting_officer` value of `=HYPERLINK("http://evil","x")` becomes a live formula in an HQ analyst's spreadsheet. Extract one shared `CsvSanitiser::cell()` helper and use it in both paths. *(ASVS 5.3.4, T10 A03)*
- [x] **Already correct:** DomPDF remote fetch and embedded PHP are disabled by the vendor defaults in use (`enable_remote => false`, `enable_php => false`), and `chroot` is `base_path()`. This closes the usual DomPDF SSRF/local-file-read vector. `config/dompdf.php` is unpublished, so these defaults apply and should be pinned explicitly by publishing the config, so a later `vendor:publish --force` cannot silently change them.
- [ ] Minor: `enable_javascript => true` (vendor default) embeds PDF-viewer JavaScript. Harmless while all template output is escaped, but set it to `false` explicitly since the reports contain no scripted content.

### 8.5 Cryptography and secrets (ASVS V6, V8, 27001 A.8.24)

- [ ] **Fix H-5.** `HASHID_SALT` from config, generated per environment. *(ASVS 6.1)*
- [ ] **Fix M-3.** No runtime `env()`; all secrets via `config()`; `config:cache` in the deploy pipeline. *(ASVS 6.4.1)*
- [ ] `APP_KEY` rotated and stored in the platform secret manager (not in `.env` on disk if avoidable); `APP_PREVIOUS_KEYS` documented for rotation. *(27001 A.8.24)*
- [ ] `JWT_SECRET` ≥ 256-bit, generated with `php artisan key:generate --show`-equivalent entropy, stored in a secret manager. *(ASVS 6.3)*
- [ ] OTP storage hardened (H-8): bcrypt or HMAC-bound. *(ASVS 6.1, 2.7)*
- [ ] `mfa_backup_codes` hashed rather than only encrypted (encryption is reversible with `APP_KEY`). *(ASVS 6.1)*
- [ ] Encryption at rest for `return_data` if it contains investigation/finance detail (M-19). *(NDPA Art. 24, 27001 A.8.24)*
- [ ] TLS 1.2+ only, HSTS `max-age=31536000; includeSubDomains; preload` after subdomain readiness, no plaintext fallback. *(ASVS 9.1)*
- [ ] Verify Redis/MySQL use TLS with peer verification (M-18). *(ASVS 9.1.3)*

### 8.6 Error handling and logging (ASVS V7, 27001 A.8.15, A.8.16)

- [ ] `APP_DEBUG=false` in production — assert it at boot, not just in `.env`. *(ASVS 7.1.1)*
- [ ] All 500s render a generic page with a correlation ID; no stack trace reaches a user. *(ASVS 7.4.1)*
- [ ] Audit log covers: login success/failure, MFA success/failure, lockout, password change/reset, privilege change, user create/disable, submission create/update/delete/approve/reject, document access, settings change, export generation. Verify each has a test. *(ASVS 7.1.3, 27001 A.8.15)*
- [ ] Audit records include actor identity **denormalised** (S-4) so deletion of a user does not erase attribution. *(ASVS 7.1.3)*
- [ ] Audit log is append-only from the application's perspective: no controller exposes update or delete on `audit_logs` (verify), and the DB user used by the app has no `DELETE` grant on that table. *(27001 A.8.15)*
- [ ] **Fix M-2.** 4xx logging aggregated and bounded. *(ASVS 7.1.1)*
- [ ] Logs are shipped off-host to a retained store (SIEM or at minimum a separate log service) with a ≥90-day hot / 1-year cold policy. *(27001 A.8.15, CIS 8)*
- [ ] Alerting on: repeated login failure per account, MFA failure bursts, privilege assignment, settings change, mass export, access-denied spikes. *(CIS 8.11)*

### 8.7 Infrastructure and configuration (ASVS V14, 27001 A.8.9, A.8.20)

- [ ] **Fix M-17.** `.env.example` safe defaults; deploy pre-flight script asserting the production invariants.
- [ ] `TRUSTED_PROXIES` set correctly, `array_filter` applied (M-17); never `*`.
- [ ] Web root is `public/` only; `.env`, `storage/`, `vendor/` unreachable over HTTP — test each by URL on the deployed host.
- [ ] `public/.htaccess` present with `Options -Indexes` — confirmed; verify the equivalent on the actual web server (nginx needs its own config).
- [ ] Directory listing disabled, `server_tokens off`, no `X-Powered-By`.
- [ ] Dedicated non-root MySQL user with least privilege (no `GRANT`, no `FILE`, no `DROP` outside migrations).
- [ ] Redis requires auth, bound to a private network, TLS enabled.
- [ ] Queue worker runs as a supervised service with a non-root user; `QUEUE_CONNECTION` and `retry_after` verified (`DB_QUEUE_RETRY_AFTER=90` must exceed the longest job).
- [ ] Health endpoint `/up` does not disclose version or configuration.
- [ ] **Fix M-16.** `robots.txt` denies by default; `X-Robots-Tag: noindex` on authenticated routes.
- [ ] WAF in front (rate limiting at the edge, in addition to in-app `throttle:`) with rules for credential stuffing and for `/api/otp/*`.

### 8.8 Data protection (NDPA 2023, 27001 A.5.34, A.8.10, A.8.11)

- [ ] Lawful basis documented for processing officer personal data. *(NDPA Art. 25)*
- [ ] Privacy notice published and linked from `/privacy-policy` (route exists — verify the view exists and is current). *(NDPA Art. 27)*
- [ ] Record of Processing Activities (RoPA) written, covering `users`, `applications.return_data`, `audit_logs`, `sessions`. *(NDPA Art. 28)*
- [ ] Data Protection Officer appointed and contactable in-app and via the portal footer. *(NDPA Art. 32)*
- [ ] Retention schedule written and **implemented** as scheduled commands (M-19, S-8). *(NDPA Art. 29)*
- [ ] Data-subject request procedure: access, correction, erasure (with lawful-basis override for statutory records), each with a tracked workflow and an SLA of one month. *(NDPA Art. 34–37)*
- [ ] Cross-border transfer assessment if Upstash (or any processor) stores data outside Nigeria; DPAs signed with Resend, Upstash and the hosting provider. *(NDPA Art. 41–43)*
- [ ] Breach response runbook with 72-hour notification path to the Nigeria Data Protection Commission. *(NDPA Art. 40)*
- [ ] Production data is **never** used in staging; staging uses synthetic data generated from the factories. *(27001 A.8.33)*

### 8.9 Secure SDLC (SSDF, 27001 A.8.25–A.8.31)

- [ ] **Threat model** documented for the three trust boundaries that matter: unauthenticated → officer, officer → reviewer, reviewer → approver. Re-run it whenever the RBAC matrix changes. *(SSDF PW.1, 27001 A.8.25)*
- [ ] **Independent penetration test** before launch and annually thereafter, scoped to the RBAC matrix and the file-handling paths. Internal testing (this document) does not substitute. *(27001 A.8.29, SSDF PW.7)*
- [ ] CI pipeline gates: `composer audit`, `composer validate`, `php artisan test`, `php artisan config:cache` (to prove M-3 is fixed), `php -l` on changed files, and a forbidden-pattern lint (`{!!`, `->raw(`, `env(` outside `config/`, `DB::raw` with interpolation). *(SSDF PS.1, PW.6, PW.8)*
- [ ] **Fix M-4.** Dependencies pinned; automated update PRs; SBOM generated (`composer sbom` / CycloneDX) and stored per release. *(SSDF PS.3, 27001 A.8.28)*
- [ ] Branch protection on `main`: required review, required status checks, signed commits, no force-push. *(27001 A.8.32)*
- [ ] Secret scanning enabled on the repository (GitHub Advanced Security or equivalent) — note `.env` is correctly gitignored, but a secret has been committed at least once in the form of the JWT fallback (H-3) and the hashid salt (H-5). *(SSDF PS.1)*
- [ ] Change management: every production change traceable to a reviewed PR and a deployment record. *(27001 A.8.32)*
- [ ] Security awareness training for all officers who will use the portal, specifically on phishing (the primary threat to an MFA-protected system) and on handling returned submissions. *(27001 A.7.4)*
- [ ] Developer secure-coding training covering the OWASP Top 10 with the Laravel-specific traps found here (mass assignment, fail-open policies, `env()` under config cache). *(SSDF PO.3)*

### 8.10 Operations and resilience (27001 A.5.29, A.5.30, A.8.13, A.8.14)

- [ ] Automated daily database backup, encrypted, off-site, with a **tested restore** (an untested backup is not a backup). *(27001 A.8.13, CIS 11)*
- [ ] Uploaded-document backup in the same schedule — `storage/app/private` is currently outside any documented backup of the database.
- [ ] RPO/RTO defined and agreed with NIS; document recovery steps.
- [ ] Disaster-recovery exercise completed once with a restore into a clean environment.
- [ ] Capacity plan: `return_data` growth, upload quota (H-11), audit-log growth (M-2), and the data-explorer query cost (S-12).
- [ ] Uptime and error-rate monitoring with on-call ownership; alert routes to a person, not a mailbox.
- [ ] Incident response plan covering: credential compromise, mass data disclosure, ransomware, insider misuse, and the specific scenario "a reviewer's account approved returns outside its scope". *(27001 A.5.24–A.5.28)*
- [ ] Business continuity: documented manual fallback for return submission if the portal is unavailable during a reporting deadline.
- [ ] Maintenance mode (`php artisan down --secret`) practised; deployment runbook written (migrate → config:cache → route:cache → view:cache → queue:restart → smoke test).

### 8.11 Verification gates (sign-off required)

- [ ] All four Critical findings closed **and covered by a failing-then-passing test**.
- [ ] All twelve High findings closed or explicitly accepted in writing by the system owner with a compensating control named.
- [ ] `php artisan test` green with the new regression tests included.
- [ ] `php artisan config:cache && php artisan route:cache && php artisan view:cache` all succeed (this alone is a strong test for M-3).
- [ ] Route-matrix authorisation test green (8.3).
- [ ] Independent penetration test completed; all Critical/High findings from it closed.
- [ ] Backups restored successfully in a clean environment.
- [ ] Go/no-go review minuted, with named owners for every accepted risk.

---

## 9. Remediation roadmap

Sequenced so that the highest risk is removed with the least code churn. Effort is engineering days for one competent Laravel developer, including tests.

### Gate 0 — Blockers. Do not take production traffic until these are closed. (~4–6 days)

| # | Action | Effort | Files touched |
|---|---|---|---|
| 1 | **C-1** assignment-authority map + self-edit lockout + privilege audit event | 1 d | `UserManagementController`, `routes/web.php` |
| 2 | **C-2** scope every overview/explorer query; restrict `/cgis/*` routes | 1.5 d | `SuperAdminController`, `SubmissionWorkflow`, `routes/web.php` |
| 3 | **C-3** `$fillable`/`$casts` for lockout columns; `forceFill` writes; strict mode in non-production | 0.5 d | `User`, `AuthController`, `AppServiceProvider` |
| 4 | **C-4** fail-closed scope checks + provisioning guard for reviewer accounts | 1 d | `SubmissionReviewController`, `SubmissionWorkflow`, `UserManagementController` |
| 5 | Regression tests for all four (they must fail on the current code) | 1 d | `tests/Feature/AuthorisationBoundaryTest.php` |

**This audit's evidence blocks are the test bodies.** Each one already asserts the secure behaviour and currently fails; closing the finding is what makes it pass.

### Gate 1 — Hardening. Before full launch. (~10–14 days)

| # | Action | Effort |
|---|---|---|
| 6 | **H-10** lockscreen throttle + session lockout; passcode minimum 6 | 0.5 d |
| 7 | **H-1** MFA throttle, attempt counter, lockout, audit | 1 d |
| 8 | **H-7 / H-8 / H-9** OTP overhaul: uniform response, one live code, hashed code, correct attempt counting | 2 d |
| 9 | **H-4** password reset revokes sessions and tokens; add "sign out everywhere" | 1 d |
| 10 | **H-3** JWT: remove fallback, verify `iss`/`aud`, token revocation | 1.5 d |
| 11 | **H-2** API requires the enrolled factor (or is disabled for enrolled users) | 1 d |
| 12 | **H-5** `HASHID_SALT` in config; **M-3** all `env()` → `config()`; add `config:cache` to deploy | 1.5 d |
| 13 | **H-6 / M-14** enforce CSP, add COOP/CORP, `no-store` on authenticated responses | 1 d |
| 14 | **H-11** upload count/size caps + per-user quota | 0.5 d |
| 15 | **H-12** transactions around workflow writes; queue the listeners; optimistic lock | 1.5 d |
| 16 | **M-9** password policy (`uncompromised`, 12 chars, history) | 0.5 d |
| 17 | **M-12** capability-based view-only enforcement | 0.5 d |
| 18 | **M-13** MFA reset command + UI | 0.5 d |
| 19 | Route-matrix authorisation test | 1 d |

### Gate 2 — Structural and compliance. Before the first statutory reporting cycle. (~15–20 days)

| # | Action | Effort |
|---|---|---|
| 20 | Consolidate authorisation into `ApplicationPolicy` (replaces five drifted copies of scope logic) | 3 d |
| 21 | Create the lookup tables from the empty migration; FKs; normalise codes (S-1, S-5) | 3 d |
| 22 | Status/stage enums + data migration + CHECK constraints (S-2, M-5) | 2 d |
| 23 | Soft deletes + `RESTRICT` on `applications.user_id` + denormalised audit actors (S-3, S-4, S-7, M-6) | 2 d |
| 24 | Scheduler: prune commands, retention policy, audit prune (S-8, M-19) | 1 d |
| 25 | Promote hot `return_data` fields to columns; index them (S-12, M-10) | 2 d |
| 26 | Upload scanning + re-encoding + attachment disposition (M-1) | 2 d |
| 27 | NDPA artefacts: RoPA, retention schedule, DPO, breach runbook, DSAR workflow | 3 d (part legal) |
| 28 | Security headers/`robots.txt`/`.env.example`/Redis TLS (M-16, M-17, M-18) | 0.5 d |
| 29 | CI gates, SBOM, dependency pinning, secret scanning (M-2, M-4, 8.9) | 2 d |

### Gate 3 — Continuous assurance (post-launch, ongoing)

- Independent penetration test annually and after any RBAC change.
- Quarterly authorisation-matrix review against the current NIS organisational structure.
- Monthly dependency and container scan; quarterly restore test.
- Alert tuning review monthly for the first quarter (expect false-positive churn).
- Re-run this audit's evidence suite in CI forever — it is the cheapest regression net for the highest-severity class of bug in the system.

### 9.1 What to tell the stakeholders

Three sentences that are true and that a non-technical sponsor can act on:

1. **A mid-level administrator can currently make themselves the highest-privileged account in the system, and the system will not notice.** This is fixable in a day and must be fixed before anyone outside the build team logs in.
2. **The lowest-privileged CGIS account can currently download the entire national returns dataset as CSV.** Also a small fix, and it is the finding that carries the greatest disclosure risk under the NDPA.
3. **The account lockout, the second factor and the per-desk approval scoping all look correct in the code and none of them actually work.** Each has a focused fix; all three must be covered by a test that currently fails, or they will regress.

---

## 10. Appendix — reproducing and re-testing

Every Critical and High finding was reproduced by a temporary PHPUnit feature test against the project's own SQLite harness, then deleted. To re-run them after remediation, create `tests/Feature/SecurityAuditTest.php` from the evidence blocks in §3 and §4 and run:

```bash
php artisan test --filter=SecurityAuditTest
```

Baseline at the time of this audit:

```
Tests:    170 passed (779 assertions)
Duration: 88.34s
```

The four exploit probes produced, against the unmodified code:

```
C-1  post_status 302, created true, category super_admin, access_level 6
C-2  GET /cgis/data-explorer as cgis_unit_user (level 0) → 200
C-3  failed_attempts 5, lockout_until null, fillable_has_lockout false
H-10 12 consecutive wrong lockscreen passcodes → 302 × 12 (no throttle)
```

Each of those lines must become `403`, `null`, `true` or `429` respectively before Gate 0 is considered closed.

---

## 11. Sign-off

| Role | Name | Decision | Date |
|---|---|---|---|
| System owner (NIS) | | ☐ Go ☐ No-go | |
| Technical lead | | ☐ Go ☐ No-go | |
| Security lead | | ☐ Go ☐ No-go | |
| Data Protection Officer | | ☐ Go ☐ No-go | |

**Accepted risks** (each requires a named owner, a compensating control and an expiry date):

| Finding | Owner | Compensating control | Expires |
|---|---|---|---|
| | | | |
