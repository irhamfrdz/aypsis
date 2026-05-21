# AYPSIS — PT. Alexindo YakinPrima Shipping Management System

## Stack
- Laravel 11 / PHP 8.2+ / MySQL (production) / SQLite (default dev)
- Blade + Tailwind 3 + Vite + FontAwesome 7 + jQuery 3
- Custom auth (AuthController), custom dynamic permission system (Gates via `permissions` table)
- DOMPDF, PhpSpreadsheet, maatwebsite/Laravel-Excel

## Commands
```bash
# Dev (runs serve + queue + logs + vite concurrently)
composer dev

# Build frontend
npm run build
npm run dev        # vite dev server

# Tests
php artisan test
php artisan test --filter=SomeTest

# Code style (Laravel Pint)
./vendor/bin/pint

# Generate permissions
php artisan permission:sync
```

## Architecture

### Middleware chain (authenticated routes)
`auth` → `EnsureKaryawanPresent` → `EnsureUserApproved` → `EnsureCrewChecklistComplete`

### Custom middleware aliases (bootstrap/app.php)
- `role` → EnsureRole
- `permission` → EnsurePermission
- `permission-like` → EnsurePermissionLike
- `only.kiky` → EnsureKikyUser (user-specific)

### Permission system
- Gates defined dynamically in `AppServiceProvider` from DB `permissions` table
- Route-level via `can:permission-name` or `middleware:permission`
- Matrix-based permission configs in `config/permission_matrix.php`, `config/permission_groups.php`

### Observers (registered in AppServiceProvider)
- `KontainerObserver`, `StockKontainerObserver`, `UangJalanObserver`
- `DaftarTagihanKontainerSewaObserver`

### Key directories
- `app/Models/` — 269 Eloquent models
- `app/Http/Controllers/` — 232 controllers
- `app/Exports/` — 55 export classes
- `app/Imports/` — 18 import classes
- `app/Console/Commands/` — 78 artisan commands
- `app/Services/` — ApprovalService, CoaTransactionService, PermissionService
- `app/Helpers/` — FormatHelper, PermissionHelper, PermissionMatrixHelper, Terbilang
- `routes/web.php` — All web routes (~2500 lines)
- `config/permissions.php` — Permission definitions

## Quirks & Gotchas
- CSRF disabled in `Kernel.php` for `testing` environment; VerifyCsrfToken middleware is commented out in web group
- `composer.json` has `disable-tls: true` and `secure-http: false` (SSL workaround, Docker uses `composer install`)
- Queue/Cache/Session all use `database` driver by default
- Timezone: `Asia/Jakarta` in `.env`
- 1129 migration files — long-running active project; schema changes are incremental
- `.env` uses MySQL; `.env.example` defaults to SQLite
- `routes/` has backup route files (`web.php.backup`, `web.php.new`, etc.) — ignore them

## Testing
- PHPUnit 11, 6 feature + 4 unit tests + 1 manual test script
- Test namespace: `Tests\` (PSR-4 autoloaded)
- CSRF automatically disabled in testing env (Kernel.php constructor)

## Deployment
- Railway via `railway.toml` (nixpacks builder, health check at `/`)
- Docker: `docker build -t aypsis .`
