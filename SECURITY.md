# GearGuard — Security Documentation
### COMP50016: Server-Side Programming II | CB016123

This document maps each known web application threat to the specific
Laravel 12 mechanism used to mitigate it in GearGuard.

---

## 1. SQL Injection

**Threat:** An attacker submits malicious SQL through a form field (e.g.
`' OR 1=1 --`) to manipulate database queries and access unauthorised data.

**Mitigation:** Laravel's Eloquent ORM uses PDO prepared statements
for every query. No raw SQL string concatenation exists in this codebase.
All user input passes through parameterised bindings automatically.

**Where in code:** `app/Models/Equipment.php` — `scopeAvailable()`,
`isAvailableFor()`. `app/Models/Booking.php` — all query scopes.
Every `Equipment::where(...)` and `Booking::create([...])` call uses
bound parameters internally.

---

## 2. Cross-Site Scripting (XSS)

**Threat:** An attacker injects JavaScript into user-supplied content
(e.g. an equipment name) which then executes in other users' browsers,
stealing session cookies or redirecting users.

**Mitigation:** Laravel's Blade templating engine auto-escapes all
output rendered with `{{ }}` syntax using `htmlspecialchars()`.
This means even if an attacker stores `<script>alert(1)</script>`
as an equipment name, it renders as harmless text in the browser.

**Where in code:** Every Blade view (`resources/views/**/*.blade.php`)
uses `{{ $variable }}` — never `{!! $variable !!}` for user-supplied data.

---

## 3. Cross-Site Request Forgery (CSRF)

**Threat:** A malicious website tricks a logged-in user's browser into
submitting a forged request (e.g. approving a booking or deleting an
item) without the user's knowledge.

**Mitigation:** Laravel automatically generates and validates a CSRF
token for every state-changing form request (POST, PUT, PATCH, DELETE).
The `@csrf` directive in every form injects this hidden token. Requests
without a valid token are rejected with a 419 error before reaching
any controller.

**Where in code:** Every form in `resources/views/` includes `@csrf`.
Laravel's `VerifyCsrfToken` middleware (registered by default) validates
all incoming POST requests.

---

## 4. Insecure Direct Object Reference (IDOR) / Broken Access Control

**Threat:** A client-role user manually types `/owner/dashboard` in
the browser URL bar, bypassing the login page and accessing admin
functionality directly. This was a known vulnerability in the SSP1
submission where `$_GET['status']` was passed directly to `updateStatus()`.

**Mitigation (1) — Route Middleware:** All owner routes are wrapped in
`middleware(['auth', 'role:owner'])`. The custom `CheckRole` middleware
checks `$request->user()->role === 'owner'` and aborts with 403 if not.

**Mitigation (2) — Status Allowlist:** `BookingController::updateStatus()`
validates `$request->status` against `['required', 'in:approved,rejected,completed']`.
No raw user-supplied value ever reaches the database.

**Mitigation (3) — Ownership Check:** `BookingController::cancel()` verifies
`$booking->user_id === auth()->id()` before allowing a cancellation, preventing
a user from cancelling another user's booking by guessing IDs.

**Where in code:** `app/Http/Middleware/CheckRole.php`,
`routes/web.php` (route groups), `app/Http/Controllers/BookingController.php`.

---

## 5. Broken Authentication & Session Management

**Threat:** Weak password storage, session fixation, or insecure
cookie handling allows attackers to hijack user accounts.

**Mitigation:**
- **Password Hashing:** Laravel uses `bcrypt` by default (via the `hashed`
  cast on `User::$casts`). Passwords are never stored in plaintext.
  The work factor is configurable in `.env` (`BCRYPT_ROUNDS=12`).
- **Session Security:** Sessions use the `database` driver (not files).
  Session cookies are `HttpOnly` (inaccessible to JavaScript) and
  `SameSite=Lax` (blocks cross-origin CSRF). Laravel rotates the
  session ID on login to prevent session fixation.
- **Jetstream:** Handles registration, login, logout, and password
  reset flows with industry-standard security built in.

**Where in code:** `app/Models/User.php` — `'password' => 'hashed'` cast.
`.env.example` — `SESSION_DRIVER=database`, `BCRYPT_ROUNDS=12`.
`composer.json` — `laravel/jetstream` dependency.

---

## 6. Race Condition / Double-Booking

**Threat:** Two users simultaneously check availability for the same
item on the same dates, both see "Available", and both successfully
book — creating a double-booking conflict.

**Mitigation:** `BookingController::store()` wraps the availability
check and insert in a single `DB::transaction()` with `lockForUpdate()`.
This acquires a database-level row lock, ensuring only one transaction
can proceed at a time. The second request waits, then re-checks
availability inside the transaction and finds a conflict.

**Where in code:** `app/Http/Controllers/BookingController.php` —
`store()` method, `DB::transaction(function() { ... ->lockForUpdate()->exists() ... })`.

---

## 7. API Authentication (Sanctum)

**Threat:** Unauthenticated users access the REST API to read private
booking data or create fraudulent bookings programmatically.

**Mitigation:** Laravel Sanctum issues hashed personal access tokens.
All API routes (except `/api/login`) require a valid Bearer token via
`middleware('auth:sanctum')`. Tokens are issued with ability scopes —
clients receive `['read:equipment', 'read:bookings', 'write:bookings']`
while owners receive additional `write:equipment` ability.
Token abilities are checked with `$request->user()->tokenCan(...)`.

**Where in code:** `routes/api.php` — `middleware('auth:sanctum')` group.
`app/Http/Controllers/Api/ApiController.php` — `login()`, `logout()`,
ability-scoped token issuance.

---

## 8. Mass Assignment Protection

**Threat:** An attacker submits extra fields in a POST request
(e.g. `role=owner`) to overwrite protected model attributes.

**Mitigation:** Every Eloquent model defines a strict `$fillable` array.
Only explicitly listed fields can be mass-assigned. The `role` field
is NOT in `Equipment::$fillable` or `Booking::$fillable`, and `role`
in `User` is only set programmatically during registration seeding.

**Where in code:** `app/Models/User.php`, `Equipment.php`, `Booking.php`
— `protected $fillable = [...]` in each.

---

## Summary Table

| Threat | OWASP Category | Laravel Mechanism | Location |
|---|---|---|---|
| SQL Injection | A03 | Eloquent PDO parameterised queries | All Models |
| XSS | A03 | Blade `{{ }}` auto-escaping | All Views |
| CSRF | A01 | `@csrf` + `VerifyCsrfToken` middleware | All Forms |
| IDOR / URL Hacking | A01 | `CheckRole` middleware + allowlist validation | Middleware, BookingController |
| Weak Passwords | A07 | BCrypt hashing (rounds=12) | User model |
| Session Hijacking | A07 | HttpOnly, SameSite cookies, DB sessions | Laravel config |
| Race Condition | A04 | `DB::transaction()` + `lockForUpdate()` | BookingController |
| Broken API Auth | A07 | Sanctum token + ability scopes | ApiController |
| Mass Assignment | A04 | `$fillable` on all models | All Models |

## Security Testing Evidence
* **IDOR (Insecure Direct Object Reference):** Logged in as a standard Client and manually navigated to `http://localhost:8000/owner/dashboard`. Verified the system correctly intercepted the request and returned a `403 FORBIDDEN` abort screen.
* **CSRF (Cross-Site Request Forgery):** Removed the `@csrf` Blade directive from the equipment booking form using DevTools and submitted a POST request. Verified the system threw a `419 PAGE EXPIRED` exception, proving state-changing requests are protected.
* **API Access:** Attempted a GET request to `/api/v1/equipment` without a Bearer token. Received `401 Unauthorized`. 

## Rate Limiting (Brute Force Protection)
Configured within `FortifyServiceProvider`, authentication routes are protected by Laravel's Rate Limiter. The system restricts users to **5 login attempts per minute** based on their email and IP address, mitigating automated brute-force credential stuffing.
