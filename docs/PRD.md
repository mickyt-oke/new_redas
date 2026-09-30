# NIS REDAS — Product Requirements Document (PRD)

**Product:** NIS REDAS — *Returns Data Automation System*
**Organization:** Nigeria Immigration Service (NIS)
**Platform:** Web application (PWA-enabled) + companion JSON API
**Version:** 1.0 (as-built documentation)
**Author:** Greatminds

---

## 1. Overview

NIS REDAS is a centralized system that automates the collection, review, and approval of periodic **returns** (operational reports) across the Nigeria Immigration Service. Officers at state commands and directorates submit structured returns; those returns flow up a multi-stage approval chain — desk supervisor → zonal command → headquarters — until they receive final approval from an HQ admin and are archived at HQ. The system enforces strong authentication (MFA for web, OTP + JWT for API), location-based access control (ABAC), and full audit logging.

### 1.1 Problem statement

- Returns are currently collected and consolidated manually, making them slow, error-prone, and hard to audit.
- There is no single authoritative register of who submitted what, when, and what happened to it.
- Sensitive personnel/operational data requires strong access control (geo-fencing to Nigeria, MFA) and a verifiable audit trail.

### 1.2 Goals

- Digitize return submission for state commands and all 10 HQ directorates.
- Enforce a deterministic, auditable multi-stage approval workflow.
- Give HQ a consolidated view: register, analytics, archive, and exportable reports (Excel/PDF).
- Provide a public, searchable directory of NIS formations.
- Harden access: MFA, OTP-based API auth, JWT token rotation, ABAC geo enforcement, audit logging.

### 1.3 Non-goals (current scope)

- Self-service registration (accounts are provisioned by HQ admins only).
- Real-time collaboration, in-app chat, or external API publishing.
- Mobile-native apps (the PWA + JSON API cover mobile clients).

---

## 2. Users and roles

The system models access on three axes: **user_category** (functional role), **primary_location_type** (org placement), and **access_level** (0–7 seniority).

| Category | Level | Description |
|---|---|---|
| `state_user` | 0 | Officer at a state command; submits state returns. |
| `desk_admin` | 1–2 | State supervisor; first review of state returns; stamps zonal routing. |
| `directorate_user` | 0 | Officer in one of the 10 HQ directorates; submits directorate returns. |
| `directorate_admin` | 3 | First-line approver of directorate returns. |
| `zonal_commander` | 4 | Approves state returns at the zonal command stage. |
| `cgis_unit_user` | 0 | Officer in one of the 7 CGIS units; submits unit returns. |
| `cgis_desk_admin` | 2 | Dedicated first-line approver of a CGIS unit's returns. |
| `hq_admin` | 5 | Headquarters approver/administrator: **final approver of all returns** (state, directorate, and CGIS paths all end at the `hq_review` stage). Owns the `/admin/submissions` review queue (approve/reject), user management, settings, audit log, consolidated reports, analytics, archive. |
| `admin` | 5 | General administrator, **view-only**: read access to all returns of any status/category/location plus the consolidation page (`/admin/consolidation`). No approve/reject, no user management. |
| `super_admin` | 6 | Executive, strictly view-only: visual dashboard (`/superadmin/dashboard`, Chart.js KPIs/charts) and returns register/detail (`/superadmin/returns`). No other operations. |
| `executive` / `cgis` | 7 | Recognized by the code (executive dashboard), not yet exposed in user management UI. |

**Directorate forms (10):** HRM, PRS, Finance, Investigation, Passport, Visa, Migration, Border, ICT, Works & Logistics.
**CGIS units (7):** ACTU, EPMS, Hostmanship, PRO/Media, Protocol, Provost, SERVICOM — each with its own dashboard and return form under `/user/cgis-units/`.

---

## 3. Functional requirements

### 3.1 Authentication & account lifecycle

| ID | Requirement |
|---|---|
| FR-AUTH-1 | Users log in with username (email **or** service number `NIS/XXX/0000`) + password. |
| FR-AUTH-2 | Login is rate-limited (5 attempts / 60 s per identity+IP; route-level 10/min). |
| FR-AUTH-3 | Disabled accounts (`is_enabled = false`) cannot log in. |
| FR-AUTH-4 | ABAC geo check at login: non-HQ users must connect from an allowed country (default Nigeria) and, for state users, from their assigned state. |
| FR-AUTH-5 | MFA (TOTP, e.g. Google Authenticator) is **mandatory**: first login forces setup (QR code + 8 single-use backup codes); subsequent logins require a TOTP or backup code. |
| FR-AUTH-6 | Self-registration is disabled; HQ admins create accounts, which triggers an email verification magic link (single-use, 1 h TTL). |
| FR-AUTH-7 | Password reset via enumeration-safe magic link (single-use, 1 h TTL, min 8 chars confirmed). |
| FR-AUTH-8 | Users can change their own password from the profile page (current password required). |
| FR-AUTH-9 | API clients authenticate with login+password+role → 4-digit email OTP (5 min TTL, max 5 attempts) → JWT access token (15 min) + refresh token (30 days, rotating, revocable). |

### 3.2 Returns submission

| ID | Requirement |
|---|---|
| FR-SUB-1 | State users submit returns (monthly, quarterly, biannual, annual, special) with command, period, return type, officer details, and consent. The state return form embeds **all ten directorate sections** as tabs (shared partials `user.states.sections.{slug}`), with field names namespaced per directorate in `return_data` (e.g. `passport[staff_strength]`, `works[staff_strength]`). Pre-submission preview is a separate read-only page (`POST /user/returns/preview`, opened in a new tab). |
| FR-SUB-1a | The state dashboard (`/user/dashboard`) is user-specific: real per-user metrics, own submissions, own notifications, and a 6-month submission trend — no mock data. State routes are restricted to `state_user` accounts at `state` locations. |
| FR-SUB-2 | Directorate users submit directorate-specific forms (10 directorates), optionally with supporting documents (≤ 20 MB, stored under `supporting-documents/{slug}`). |
| FR-SUB-2a | CGIS unit users submit unit-specific forms (7 units) under `/user/cgis-units/{slug}` with the same validation, upload, and resubmission capabilities. |
| FR-SUB-3 | Each submission becomes an `Application` record with a JSON snapshot of the form (`return_data`) and an audit trail (`workflow_path`). |
| FR-SUB-4 | Submitters can view, print, and download their own submissions (PDF), and edit/resubmit or **delete** them while they are not yet approved; approved returns are locked. Delete removes uploaded files from storage. |
| FR-SUB-5 | Passport forms get dropdowns populated from the NIS directory (processing centres, foreign missions). |

### 3.3 Approval workflow

| ID | Requirement |
|---|---|
| FR-WF-1 | State path: `desk_review → zonal_review → hq_review → approved` (final approval by `hq_admin`). |
| FR-WF-2 | Directorate path: `directorate_review → hq_review → approved`. |
| FR-WF-2a | CGIS unit path: `cgis_desk_review → hq_review → approved`, with a dedicated `cgis_desk_admin` per unit as first-line approver. |
| FR-WF-3 | Each approver only sees submissions in their scope (state code, directorate slug, or zone). |
| FR-WF-4 | Desk-admin approval stamps the `zonal_code` so the return routes to the correct zonal commander. |
| FR-WF-5 | Approvers can approve (advance one stage; optional note) or reject (return to originator with a mandatory comment; status `returned`). Approval notes and rejection reasons are persisted to the `application_comments` history thread. |
| FR-WF-6 | Resubmission re-enters the first stage with a `resubmitted` path entry and a `resubmitted` comment-history record. |
| FR-WF-7 | Approver dashboards show the pending queue plus approved/returned counts. |
| FR-WF-8 | Approvers can view a submission preview (with the full review-history thread), stream attached documents, and export a single return or filtered report as CSV/PDF. |

### 3.4 HQ administration

| ID | Requirement |
|---|---|
| FR-HQ-1 | HQ dashboard: totals, awaiting-HQ counts, per-directorate and per-CGIS-unit aggregates, recent returns. |
| FR-HQ-2 | Return register: filterable/paginated (formation, category, status, stage, period, free-text search). |
| FR-HQ-3 | Single-return view with workflow timeline resolved to actor names. |
| FR-HQ-4 | Archive of approved returns. |
| FR-HQ-5 | Analytics: per-directorate bar chart, status doughnut, 12-month trend, average stage turnaround. |
| FR-HQ-6 | Consolidated Excel reports (quarterly / biannual / annual), generation audit-logged. |
| FR-HQ-7 | User management: create/edit users, assign access profile (category, location, scope codes), enable/disable accounts, trigger email verification. |
| FR-HQ-8 | System settings: ABAC toggles (enabled, location enforcement, HQ bypass), allowed countries, geolocation provider (ip-api / ipapi), cache TTL, fallback state, API token. |
| FR-HQ-9 | Audit log viewer: filter by date, user, action, status, entity, JSON search. |
| FR-HQ-10 | Consolidation page (`/admin/consolidation`): consolidated cross-formation view of returns. Read-only admin pages (dashboard, register, archive, analytics, reports, consolidation) are shared with the view-only `admin` category; approve/reject, settings, audit log, and user management require `hq_admin`. |

### 3.5 Notifications

| ID | Requirement |
|---|---|
| FR-NTF-1 | Users can list notifications, get unread counts, and mark one/all as read (web session + JWT API). |
| FR-NTF-2 | Workflow events produce notifications: on submission and resubmission, in-scope reviewers of the pending stage are notified; on approval, the submitter and next-stage reviewers are notified; on rejection, the submitter is notified with the reason. |

### 3.6 Public features

| ID | Requirement |
|---|---|
| FR-PUB-1 | Public directory of NIS formations (8 categories: passport centres, state commands, zonal commands, border posts, airports, marine commands, training schools, foreign missions) with tabs, search, and pagination. |
| FR-PUB-2 | Terms & conditions and privacy policy pages. |
| FR-PUB-3 | Installable PWA with offline page. |

---

## 4. Non-functional requirements

| ID | Requirement |
|---|---|
| NFR-SEC-1 | MFA mandatory for all web users; OTP second factor for API clients. |
| NFR-SEC-2 | MFA secrets and backup codes encrypted at rest; OTPs and tokens stored only as SHA-256 hashes. |
| NFR-SEC-3 | Refresh tokens rotate on use and are revocable; IP and user agent recorded. |
| NFR-SEC-4 | ABAC deny-by-default middleware on every protected route (category, location, role, minimum level). |
| NFR-SEC-5 | Per-request geolocation enforcement (re-checked every 5 minutes). |
| NFR-SEC-6 | Append-only audit trail of auth events, blocked logins, CRUD, setting changes, report generation. |
| NFR-PERF-1 | Session, cache, and queue are database-backed; composite indexes on hot query paths (workflow stage + scope, notifications, audit). |
| NFR-REL-1 | Auth magic links and OTPs are single-use with bounded TTLs. |
| NFR-COMP-1 | PHP 8.2+, Laravel 12, MySQL (default) / SQLite (tests). |
| NFR-OPS-1 | Configuration via environment variables; ABAC/geolocation tunable at runtime via DB settings (no redeploy). |

---

## 5. Success metrics

- 100% of returns submitted digitally through the workflow (vs. manual channels).
- Median end-to-end approval time per stage (measurable from `workflow_path` timestamps; already surfaced in HQ analytics).
- Zero successful logins from disallowed geographies (audit-log verifiable).
- Full audit coverage of authentication and approval actions.

---

## 6. Release status

Delivered: full auth stack (web MFA, API OTP+JWT), submission workflow end-to-end, approver dashboards, HQ admin (users, settings, audit, analytics, Excel/PDF reports), public directory, PWA shell, audit logging.

## 7. Known gaps and roadmap

| ID | Gap | Notes |
|---|---|---|
| G1 | User-facing report generation and archive upload are validated stubs | Routes accept and validate input but don't persist/generate. |
| G2 | ~~CGIS unit pages exist as views but have no routes~~ **Resolved** | CGIS units now have full routing, dashboards, forms, and a `cgis_desk_review` workflow stage. |
| G3 | ~~Super-admin user management routes are placeholders~~ **Resolved** | The placeholder `/superadmin/users*` routes (and all super-admin approve/reject routes) were removed; `super_admin` is now a strictly view-only executive dashboard. Real user CRUD lives under `/admin/users` (`hq_admin` only). |
| G4 | ~~`user_notifications` has readers but no producers~~ **Resolved** | `SubmissionWorkflow` now writes notifications on submission, approval, rejection, and resubmission (`notifyPendingReviewers()` / `notifySubmitter()`). |
| G5 | `otp_login` / `otp_verify_email` email templates are referenced but not seeded | Must exist in `email_templates` or API mail send throws. |
| G6 | `NisData` model/table unused | Legacy HR-stats placeholder; decide to implement or drop. |
| G7 | Sanctum installed but unused alongside custom JWT | Consolidate on one API token system. |
| G8 | No `.env.example` in repo | Document required env vars (see TRD §10). |
| G9 | Edit-prefill doesn't repopulate pre-namespacing submissions | Submissions created before the state/directorate form namespacing (e.g. bare `staff_strength` vs `passport[staff_strength]`) won't prefill the renamed fields when edited; new submissions round-trip correctly. |
| G9 | Legacy/dead code: root `DashboardController`, `AdminDashController`, vestigial `register` view | Cleanup candidates. |
