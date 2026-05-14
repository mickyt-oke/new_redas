# TODO - JWT Refresh Auth + Harden Login/Logout

## Authentication & Tokens
- [x] Add JWT dependency to composer.json (firebase/php-jwt) and update composer.lock
- [x] Add `refresh_tokens` persistence table via migration
- [x] Implement JWT access/refresh token issuance in an API auth controller
- [x] Implement refresh endpoint with refresh-token rotation:
  - [x] Validate refresh JWT signature + expiry
  - [x] Validate refresh token jti exists, not revoked, not expired in DB
  - [x] Revoke old refresh token row
  - [x] Issue new access + refresh tokens
- [x] Implement API logout:
  - [x] Revoke refresh token in DB (best-effort)
  - [ ] Invalidate access token (best-effort; mainly remove client-side token)

## Middleware / Authorization
- [x] Add JWT access token middleware to authenticate API requests and set `$request->user()`
- [x] Update routes/api.php to protect endpoints with JWT middleware (not auth:sanctum)

## Session Hardening (Web)
- [ ] Ensure web logout properly invalidates session and regenerates CSRF token (already present; verify cookies)
- [x] Ensure API endpoints don’t return redirects (fix `/api/login` response)

## Client-side Validation Contract
- [ ] Standardize JSON error codes for token issues:
  - [ ] `401 TOKEN_EXPIRED` -> client should call `/api/refresh`
  - [ ] `401 TOKEN_INVALID` or `401 TOKEN_REVOKED` -> client should redirect to web login

## Verification
- [ ] Run migrations
- [ ] Smoke test:
  - [ ] POST /api/login returns access_token + refresh_token JSON
  - [ ] POST /api/refresh rotates refresh tokens
  - [ ] POST /api/logout revokes refresh token
  - [ ] GET /api/user works with valid access token
