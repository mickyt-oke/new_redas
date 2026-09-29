# NIS REDAS — Backend Schema

**Database:** MySQL (default DB `redas_new`, utf8mb4 / utf8mb4_unicode_ci, strict mode). SQLite in-memory for tests.
**Source of truth:** `database/migrations/`, `app/Models/`, `database/seeders/`.

---

## 1. Entity-relationship overview

```
users 1───* applications                 (user_id → users.id, CASCADE)
users 1───* applications (supervisor)    (supervisor_id → users.id, SET NULL)
users 1───* applications (last actor)    (last_action_by → users.id, SET NULL)
applications 1───* application_comments  (application_id, CASCADE)
users 1───* application_comments         (user_id → users.id, SET NULL)
users 1───* refresh_tokens               (CASCADE)
users 1───* otp_codes                    (CASCADE)
users 1───* user_notifications           (CASCADE)
users 1───* auth_tokens                  (CASCADE)
users 1───* audit_logs                   (SET NULL)
users 1───* personal_access_tokens       (polymorphic tokenable, Sanctum)

primary_location_types / primary_location_codes : standalone lookups
  (users.primary_location_type/code are plain strings — no FK enforcement)
settings, email_templates, nis_data, nis_directories : standalone
```

## 2. Application tables

### 2.1 users

Final effective schema, built incrementally by `0001_01_01_000000`, `2024_01_01_000003`, `2026_04_28_100000`, `2026_06_27_000005`, `2026_07_22_000001`, `2026_07_22_000004`, `2026_07_29_000001`, `2026_08_07_000007`, `2026_08_12_000008`.

| Column | Type | Null | Default | Constraints / notes |
|---|---|---|---|---|
| id | bigint unsigned AI | no | — | PK |
| name | varchar(255) | no | | |
| service_number | varchar(255) | yes | null | UNIQUE; format `NIS/XXX/0000` |
| role | varchar(20) | no | `'officer'` | history: ENUM(admin,zonal,state,officer) → +directorate → VARCHAR(20) |
| user_category | varchar(40) | no | `'state_user'` | composite idx `users_category_location_idx` |
| primary_location_type | varchar(20) | no | `'state'` | state / directorate / zonal / unit / headquarters |
| primary_location_code | varchar(50) | yes | null | |
| assigned_state_code | varchar(50) | yes | null | |
| assigned_directorate_code | varchar(50) | yes | null | |
| assigned_cgis_unit_code | varchar(50) | yes | null | |
| assigned_desk_admin_code | varchar(50) | yes | null | |
| assigned_zonal_command_code | varchar(50) | yes | null | |
| geo_state | varchar(10) | yes | null | INDEX `users_geo_state_idx` |
| access_level | tinyint unsigned | no | 0 | 0–7 seniority for `minLevel` checks |
| email | varchar(255) | no | | UNIQUE |
| email_verified_at | timestamp | yes | null | |
| password | varchar(255) | no | | bcrypt |
| is_enabled | boolean | no | true | |
| mfa_secret | text | yes | null | **encrypted** (model cast) |
| mfa_enabled | boolean | no | false | |
| mfa_verified_at | timestamp | yes | null | |
| mfa_backup_codes | text | yes | null | **encrypted array** (model cast) |
| mfa_last_used_at | timestamp | yes | null | |
| remember_token | varchar(100) | yes | null | |
| created_at / updated_at | timestamps | yes | | |

**Model (`User`):** hidden = password, remember_token, mfa_secret, mfa_backup_codes. Constants: `ACCESS_CATEGORIES` (state_user, desk_admin, directorate_user, directorate_admin, zonal_commander, cgis_unit_user, cgis_desk_admin, hq_admin, admin, super_admin), `LOCATION_TYPES` (state, zonal, directorate, unit, headquarters), `ROLE_TYPES` (user, officer, admin, state, zonal, directorate, unit_officer, unit_admin, super_admin). Key methods: `hasCategory`, `hasLocationType`, `hasMinimumAccessLevel`, `requiredGeoState` (geo_state fallback to state location code; null for directorate and unit), `isHeadquartersUser` (true for headquarters and unit locations), `isMfaEnabled`, `isMfaSetupPending`, `isDirectorateAccount`, `directorateSlug` (maps HRM/PRS/FIN/ICT/WKS/PAS/INV/VIS/BOR/MIG to form slugs), `cgisUnitSlug` (maps `assigned_cgis_unit_code` to the 7 CGIS unit slugs: actu, epms, hostmanship, pro-media, protocol, provost, servicom). CGIS canonical profile: cgis_unit_user = location `unit` + role `unit_officer` (level 0), cgis_desk_admin = location `unit` + role `unit_admin` (level 2); legacy accounts with role `officer`/`admin` at location `headquarters` are still accepted by the route middleware. No Eloquent relationships defined despite inbound FKs.

### 2.2 applications — the submitted "returns"

Migration: `2026_08_12_000001_create_applications_table.php`.

| Column | Type | Null | Default | Notes |
|---|---|---|---|---|
| id | bigint AI | no | — | PK |
| user_id | bigint | no | | FK → users, CASCADE |
| status | varchar(40) | no | `'pending'` | pending / approved / returned (+ legacy submitted/queried/rejected) |
| workflow_stage | varchar(60) | no | `'submitted'` | submitted, desk_review, directorate_review, cgis_desk_review, zonal_review, hq_review, approved; `admin_review` is **legacy only** — nothing routes to it (data migration `2026_08_20_000001` finalized rows stuck in it as approved) |
| workflow_path | json | yes | | audit trail of `{stage, by, at, action}` |
| category | varchar(20) | yes | | state / directorate / cgis |
| scope_code | varchar(50) | yes | | state code, directorate slug, or CGIS unit slug |
| zonal_code | varchar(50) | yes | | stamped at desk_review for zonal routing |
| return_data | json | yes | | full form snapshot incl. uploaded file paths |
| comments | text | yes | | legacy: holds only the **latest** rejection reason; full history lives in `application_comments` |
| supervisor_id | bigint | yes | | FK → users, SET NULL (schema only; not written by current workflow) |
| last_action_by | bigint | yes | | FK → users, SET NULL |
| created_at / updated_at | timestamps | yes | | |

Indexes: `applications_stage_scope_idx` (workflow_stage, scope_code), `applications_stage_zonal_idx` (workflow_stage, zonal_code), `applications_category_stage_idx` (category, workflow_stage).

**Model (`Application`):** casts `return_data`/`workflow_path` → array. Relations: `user()`, `supervisor()`, `lastActionBy()` belongsTo User; `reviewComments()` hasMany `ApplicationComment` (oldest first). Scope: `awaiting($stage)`.

### 2.3 application_comments — review-history thread

Migration: `2026_08_20_000002_create_application_comments_table.php`.

| Column | Type | Null | Notes |
|---|---|---|---|
| id | bigint AI | no | PK |
| application_id | bigint | no | FK → applications, CASCADE |
| user_id | bigint | yes | FK → users, NULL ON DELETE (actor; survives account deletion) |
| stage | varchar(60) | no | workflow stage at which the action occurred |
| action | varchar(20) | no | submitted / resubmitted / approved / rejected / note |
| comment | text | yes | rejection reason (required on reject), optional approval note |
| created_at / updated_at | timestamps | yes | |

Index: (application_id, created_at). Model: `ApplicationComment` with `ACTION_*` constants; written by `SubmissionWorkflow::recordComment()` on every workflow action; rendered as the review-history thread in desk-admin preview, owner submission, and HQ return-detail views.

### 2.4 nis_data

Migration `2026_04_28_052729`. `id` PK; `directorate_id` tinyint unsigned; `cadre` string; `male`/`female`/`total` uint default 0; `zone` string nullable; `period` date; timestamps. No indexes/FKs. **Unused by application code** (legacy HR-stats placeholder).

### 2.5 nis_directories

Migration `2026_07_17_213528`. `id` PK; `category` varchar(50) INDEX; `name` string; `state`/`country`/`city`/`region`/`type` strings nullable; `address` text; `email` string nullable; `code` varchar(20) nullable; `sort_order` smallint unsigned default 0 INDEX; timestamps. Composite index (category, sort_order); FULLTEXT on `address` (non-SQLite). Seeded with ~235 entries (44 passport centres, 41 state commands, 8 zonal commands, 19 border posts, 5 airports, 4 marine commands, 4 training schools, 110 foreign missions). Powers the public `/directory` page and passport-form dropdowns. Model scopes: `category`, `search`.

## 3. Auth & security tables

### 3.1 refresh_tokens — `2026_05_14_000001`

`id` PK; `user_id` FK → users CASCADE; `jti` varchar(64) UNIQUE; `token_hash` varchar(64) UNIQUE (SHA-256 only); `expires_at` timestamp INDEX; `revoked_at` timestamp nullable INDEX; `created_ip` varchar(45) nullable; `user_agent` text nullable; timestamps; composite index (user_id, expires_at).

### 3.2 otp_codes — `2026_06_09_000002`

`id` PK; `user_id` FK → users CASCADE; `purpose` string INDEX (login, verify_email); `code_hash` string; `expires_at` timestamp INDEX; `attempts` uint default 0; `consumed_at` timestamp nullable; timestamps; composite index (user_id, purpose).

### 3.3 auth_tokens — `2026_06_09_000004`

`id` PK; `user_id` FK → users CASCADE; `type` string INDEX (email_verify, password_reset); `token_hash` string INDEX; `expires_at` timestamp INDEX; `used_at` timestamp nullable; timestamps; composite index (user_id, type). Single-use magic-link tokens.

### 3.4 personal_access_tokens — `2026_02_20_155859` (Sanctum, dormant)

`id` PK; morphs `tokenable_type`/`tokenable_id`; `name` text; `token` varchar(64) UNIQUE; `abilities` text nullable; `last_used_at` timestamp nullable; `expires_at` timestamp nullable INDEX; timestamps.

### 3.5 audit_logs — `2026_07_22_000003`

`id` PK; `user_id` bigint FK → users SET NULL, nullable; `action` varchar(100); `entity_type`/`entity_id` varchar(100) nullable; `details` json nullable; `ip_address` varchar(45) nullable; `location` varchar(120) nullable; `user_agent` text nullable; `status` varchar(20) default `'success'`; `created_at` only (no `updated_at`). Indexes: (action, created_at), (status, created_at), (user_id, created_at), (entity_type, entity_id). Model scopes: `ofAction`, `ofStatus`, `ofEntity`.

## 4. Support tables

### 4.1 user_notifications — `2026_06_09_000003`

`id` PK; `user_id` FK → users CASCADE; `type` string INDEX (success/info/warning/danger); `title` string nullable; `description` text nullable; `tag` string nullable (Urgent, Deadline, Approved); `action_url` string nullable; `is_read` boolean default false; `payload_json` json nullable; `error` json nullable; timestamps; composite index (user_id, is_read, created_at). Producers: `SubmissionWorkflow` writes rows on submission/resubmission (to in-scope reviewers), approval (submitter + next-stage reviewers), and rejection (submitter, with reason).

### 4.2 email_templates — `2026_06_09_000001`

`id` PK; `key` string UNIQUE; `type` string INDEX (otp, workflow); `subject` string nullable; `body` longtext; `is_active` boolean default true; timestamps. Placeholders: `{{ name }}`, `{{ magic_link }}`, `{{ expires_in_minutes }}`. Seeded: `verify_email_magic_link`, `password_reset_magic_link`. **Referenced but unseeded:** `otp_login`, `otp_verify_email`.

### 4.3 settings — `2026_07_22_000002`

`id` PK; `key` varchar(100) UNIQUE; `value` text nullable; `type` varchar(20) default `'string'`; `description` text nullable; `is_public` boolean default false; timestamps. Model: `typedValue()` coercion (boolean/integer/json/string), scope `public`. Seeded keys: `abac_enabled`, `abac_location_enforcement`, `abac_hq_bypass`, `abac_allowed_countries`, `geolocation_provider`, `geolocation_cache_ttl`, `geolocation_fallback_state`, `geolocation_api_token`.

### 4.4 primary_location_types / primary_location_codes — `2026_06_27_000006`

- `primary_location_types`: `id` PK; `name` varchar(100) UNIQUE; timestamps. Rows: state, directorate, zonal, headquarters.
- `primary_location_codes`: `id` PK; `code` varchar(10) UNIQUE; `location_name` varchar(100) INDEX; timestamps. Rows: 36 Nigerian states + FCT (AB…ZA).

## 5. Framework tables (stock Laravel)

| Table | Migration | Notes |
|---|---|---|
| password_reset_tokens | `0001_01_01_000000` | email PK, token, created_at |
| sessions | `0001_01_01_000000` | string id PK, user_id indexed, payload, last_activity indexed |
| cache / cache_locks | `0001_01_01_000001` | key PK, value, expiration idx |
| jobs / job_batches / failed_jobs | `0001_01_01_000002` | database queue driver |

## 6. Seed data

**`DatabaseSeeder`** orchestrates: one active admin (`admintest@nis.gov.ng`, service number `NIS/ADM/000`, password `password123`, role admin, category `admin` (view-only general admin), HQ, geo FC, level 5), then calls `NewSeeder`, `EmailTemplateSeeder`, `PrimaryLocationTypeSeeder`, `PrimaryLocationCodeSeeder`, `SettingSeeder`, `NisDirectorySeeder`.

**`NewSeeder`** (workflow-aligned demo users, all password `password123`):

- Per 37 states: one `state_user` (level 0) + one `desk_admin` (level 1, rotating zone).
- Per 10 directorates (HRM, PRS, FIN, ICT, WKS, PAS, INV, VIS, BOR, MIG): one `directorate_user` (level 0) + one `directorate_admin` (level 3).
- Per 8 zones (ZONE-A…ZONE-H): one `zonal_commander` (level 4) + one supervisor desk_admin (level 2).
- 10 CGIS unit admins (level 5, CGIS-01…CGIS-10), 5 HQ admins (`hq_admin`, level 5), 1 general admin (`admin`, view-only, level 5, `generaladmin.seed@nis.gov.ng`), 1 super_admin (level 6, `superadmin.seed@nis.gov.ng`).
- Per CGIS unit (actu, epms, hostmanship, pro-media, protocol, provost, servicom): one `cgis_unit_user` (role `unit_officer`, level 0, `cgis.<slug>.seed@nis.gov.ng`) + one `cgis_desk_admin` (role `unit_admin`, level 2, `cgisdesk.<slug>.seed@nis.gov.ng`), both with `primary_location_type` = `unit`, `primary_location_code` = unit slug, and `assigned_cgis_unit_code` = unit slug.

**`TestUserSeeder`** (not called by DatabaseSeeder): 10 named officers × 10 directorates = 100 `directorate_user` accounts.

**Factories:** only `UserFactory` (password `password`, verified, `unverified()` state).

## 7. Schema anomalies

1. `2026_08_13_000000_create_directorate_formation_zone_headquarters_cgis_unit_tables.php` is **empty** — those tables were never created.
2. `users` ↔ `primary_location_*` lookups are enforced only in application code (no FKs).
3. The `role` column's ENUM history means constraints only ever existed on MySQL; SQLite tests accept any string.

Note: data migration `2026_08_20_000001_introduce_hq_admin_role.php` converts existing `user_category='admin'` users to `hq_admin` and finalizes applications stuck in the legacy `admin_review` stage as approved.
