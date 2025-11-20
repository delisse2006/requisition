## Overview

This is a Laravel 9 web application that manages user-submitted requisitions. Key runtime facts:
- PHP >= 8.0 (see `composer.json`).
- Frontend uses Vite (`package.json`) for `npm run dev` / `npm run build`.
- Persistence: standard Eloquent models and migrations in `database/migrations`.

The purpose of these instructions is to give AI coding agents focused, actionable knowledge to be productive immediately in this codebase.

## Architecture & Core Flows

- **Auth & Roles**: Roles are a string on the `users.role` column. See `app/Models/User.php` for helper methods: `isAdmin()`, `isAccountant()`, `isEmployee()`.
- **Role enforcement**: Role checks happen via custom middleware: `app/Http/Middleware/RoleMiddleware.php` (generic `role:admin,accountant`) and `app/Http/Middleware/AccountantMiddleware.php` (accountant-or-admin). Routes showing usage are in `routes/web.php`.
- **Requisition lifecycle**: `app/Models/Requisition.php` defines states and `canTransitionTo()` helper. Controller logic and allowed transitions live in `app/Http/Controllers/RequisitionController.php` — common transitions are `pending -> bought -> done -> paid`.
- **Notifications & Emails**: Mailables are in `app/Mail/` (`RequisitionSubmitted`, `RequisitionStatusUpdated`) and use views under `resources/views/emails/`. The controller sends emails synchronously with exception handling; don't assume queued delivery unless changed.
- **File storage**: Receipts and avatars are stored on the `public` disk. Code uses `Storage::disk('public')` and `User::getAvatarUrlAttribute()` returns `storage/avatars/...` fallbacking to UI Avatars.

## Key patterns & conventions (project-specific)

- Role checks: prefer model helpers (e.g. `auth()->user()->isAccountant()` or `isAdmin()`) rather than checking strings inline.
- Authorization rules are enforced in controllers (explicit checks) in addition to middleware — keep both checks when changing flows.
- Status transition enforcement: controller uses an explicit map (`validTransitions`) and returns validation errors for invalid transitions — follow this pattern for any workflow updates.
- Requisition numbering: generated in `RequisitionController::store()` as `REQ-<YEAR>-<zero-padded id>` (e.g. `REQ-2025-00012`) — preserve format if modifying generation logic.
- File handling: uploaded receipts are stored with `store('receipts', 'public')` and old files are deleted using `Storage::disk('public')->delete(...)`.

## Developer workflows and commands

- Install PHP deps: `composer install`.
- Prepare env (PowerShell):

```powershell
Copy-Item .env.example .env; php artisan key:generate
```

- Link public storage (required for avatars & receipts):

```powershell
php artisan storage:link
```

- Run DB migrations and seeders (development):

```powershell
php artisan migrate --seed
```

- Run app and frontend (development):

```powershell
php artisan serve; npm ci; npm run dev
```

- Tests: prefer the Laravel wrapper: `php artisan test`. `./vendor/bin/phpunit` also works in mixed shells.

## Files to inspect when making changes

- Routing & middleware: `routes/web.php`, `app/Http/Middleware/RoleMiddleware.php`, `app/Http/Middleware/AccountantMiddleware.php`
- Models & business logic: `app/Models/Requisition.php`, `app/Models/User.php`
- Controller behavior: `app/Http/Controllers/RequisitionController.php` (status transitions, email sending, file uploads)
- Mail templates: `app/Mail/*.php` and `resources/views/emails/*`
- Background helpers: `app/Services/BackgroundService.php` (image helpers used by views)
- Build/config: `composer.json`, `package.json`, `vite.config.js`, `phpunit.xml`

## Testing & safety notes

- Email sending in controllers is wrapped in try/catch and logs failures; avoid changing this behavior unless you also add retry/queue logic and update configuration.
- Do not bypass controller-level authorization checks when adding new endpoints — both middleware and controller checks are intentional.
- When updating files stored on the `public` disk, ensure old files are deleted to avoid orphaned storage (pattern present in `updateStatus`).

## Example snippets (copy-friendly)

- Role check (use model helper):

```php
if (!auth()->user()->isAccountant() && !auth()->user()->isAdmin()) {
    abort(403);
}
```

- Validate status transition (follow existing map):

```php
$validTransitions = ['pending'=>['bought'],'bought'=>['done'],'done'=>['paid'],'paid'=>[]];
if (!in_array($next, $validTransitions[$current] ?? [])) {
    return back()->withErrors(['status' => "Invalid transition from {$current} to {$next}."]);
}
```

## When to ask for human review

- Any change to role logic or `users.role` semantics (e.g., migration to permissions package).
- Switching email delivery to queues or changing mailer configuration.
- Changing storage disks, retention, or public/private access for receipts/avatars.

If anything above is unclear or you'd like more examples (unit tests, sample mutation PR, or CI steps), tell me which area to expand and I will iterate.
