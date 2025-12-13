# Copilot instructions for Play to Help (PHP MVC)

This project is a small, custom PHP MVC app (no framework). Keep changes minimal and follow existing patterns.

Key facts
- Architecture: lightweight MVC split into `Controller/`, `Model/`, `View/` and central DB config in `config/config.php`.
- DB: `config/config.php` connects to MySQL `playtohelp` on localhost (user `root`, empty password). Do not change credentials without confirming environment.
- Sessions: controllers use PHP sessions for auth. Some controllers return JSON, others render server pages and redirect.

Important patterns and examples
- Controllers expose action routing via `$_GET['action']` at the bottom of the controller file. Example: `Controller/authController.php?action=register` will run the `register()` logic.
- Auth endpoints expect JSON in `php://input` and return JSON. Example (send JSON body):

  POST `Controller/authController.php?action=register`
  Content-Type: application/json
  Body: {"firstName":"Jean","lastName":"Dupont","username":"jdup","email":"a@b.com","password":"sekret",...}

- Back-office user flows use regular HTML forms and `POST` with `application/x-www-form-urlencoded`. Example: the form in `View/BackOffice/createuser.php` posts to `index.php?action=create` following the controller pattern in `Controller/usercontroller.php`.
- Models map DB snake_case fields to model getters/setters (e.g. DB `first_name` ↔ `User::getFirstName()` / `setFirstName()`). Preserve this mapping when changing field names.

Conventions you must follow
- Keep controllers focused on request handling; business logic belongs in `Model/*` classes (Auth, User). Follow existing method names (`register`, `login`, `addUser`, `updateUser`, etc.).
- Passwords: use `password_hash()`/`password_verify()` as already implemented; do not remove hashing.
- Error handling: many controllers use `die()` or set `$_SESSION['errors']`. When adding behavior, prefer to follow the local pattern (set `$_SESSION['errors']` for UI forms, return JSON `{success:false, message:...}` for API endpoints).
- Assets: front-end assets live under `View/FrontOffice/assets` and `View/FrontOffice/vendor`; preserve relative paths used in views.

Files to inspect for examples
- DB & connection: `config/config.php` (PDO options and database name)
- Auth API: `Controller/authController.php` and `Model/Auth.php`
- User CRUD + templates: `Controller/usercontroller.php`, `Model/user.php`, `View/BackOffice/createuser.php`, `View/BackOffice/index.php`
- Frontend register/login pages: `View/FrontOffice/register.php`, `View/FrontOffice/login.php`

Developer workflows
- Run locally under XAMPP/Apache on Windows. Place project in `htdocs` (already present). Start Apache + MySQL services.
- Database: ensure a `users` table matching fields used across models (first_name, last_name, username, email, password, profile_image, stream_link, stream_description, stream_platform, role, join_date, created_at, updated_at). There is no built-in migration tool in the repo.
- Testing auth endpoints: use a JSON POST to `Controller/authController.php?action=login` / `action=register`. Use `curl` or browser `fetch`.

What not to change without coordination
- Global session behavior and DB credentials.
- The mapping between DB column names and model getters/setters.
- Auth flow: endpoints that return JSON are consumed by front-end JS; changing response shapes requires updating the JS callers.

If you need more context
- Ask for specific flows to examine (e.g., how registration is wired from `register.php` → `AuthController`).
- If you want, I can add a lightweight README or a one-file router to standardize routes.

— End of guidance —
