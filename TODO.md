# TODO - Login Redesign and Auth Flow Update

- [x] Review and simplify `resources/views/login.blade.php` to a full-width single login modal/card layout.
- [x] Remove split/auth-role UI remnants and keep only username + password fields.
- [x] Add/retain inline validation messaging for username and password with responsive behavior.
- [x] Update `app/Http/Controllers/AuthController.php` login method to validate `username` + `password` only.
- [x] Remove requested role validation/check from backend login logic while keeping MFA + ABAC + redirect behavior.
- [ ] Run focused auth tests or equivalent verification command.
- [ ] Mark all tasks complete.
