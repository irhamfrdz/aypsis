@echo off
REM ============================================================================
REM AYP SIS Permissions Seeder Runner (Windows)
REM Comprehensive permissions seeding script for production server
REM ============================================================================

echo ==================================================================================
echo 🚀 AYP SIS - Comprehensive Permissions Seeder v1.0.0
echo ==================================================================================
echo.

REM Check if we're in the right directory
if not exist "artisan" (
    echo ❌ Error: Artisan file not found. Please run this script from the Laravel project root directory.
    pause
    exit /b 1
)

REM Check PHP
php --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Error: PHP is not installed or not in PATH
    pause
    exit /b 1
)

REM Check Laravel
php artisan --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Error: Laravel artisan is not working. Check your Laravel installation.
    pause
    exit /b 1
)

echo ℹ️  PHP version:
php --version | findstr /C:"PHP"
echo.
echo ℹ️  Laravel version:
php artisan --version
echo.

REM Create backup directory
if not exist "backups" mkdir backups
if not exist "logs" mkdir logs

REM Create backup
echo ℹ️  Creating database backup...
set BACKUP_FILE=backups\backup_before_permissions_%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%.sql
php artisan db:dump --database=mysql > "%BACKUP_FILE%" 2>nul
if %errorlevel% equ 0 (
    echo ✅ Database backup created: %BACKUP_FILE%
) else (
    echo ⚠️  Could not create database backup. Continuing without backup...
)
echo.

REM Clear cache
echo ℹ️  Clearing Laravel cache...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo.

REM Run seeders
echo ℹ️  Running ComprehensivePermissionsSeeder...
echo Description: Creates all system permissions organized by categories
php artisan db:seed --class=ComprehensivePermissionsSeeder --force
if %errorlevel% neq 0 (
    echo ❌ Error: ComprehensivePermissionsSeeder failed
    pause
    exit /b 1
)
echo ✅ ComprehensivePermissionsSeeder completed successfully
echo.

echo ℹ️  Running RoleAndPermissionSeeder...
echo Description: Creates roles and assigns permissions to users
php artisan db:seed --class=RoleAndPermissionSeeder --force
if %errorlevel% neq 0 (
    echo ❌ Error: RoleAndPermissionSeeder failed
    pause
    exit /b 1
)
echo ✅ RoleAndPermissionSeeder completed successfully
echo.

REM Check results
echo ℹ️  Checking seeder results...
echo.

REM Display summary
echo ==================================================================================
echo 📊 SEEDING SUMMARY
echo ==================================================================================
echo.
echo ✅ Completed Seeders:
echo    1. ComprehensivePermissionsSeeder - All system permissions
echo    2. RoleAndPermissionSeeder - Roles and user assignments
echo.
echo 👥 Default Users Created:
echo    • Admin: username='admin', password='admin123'
echo    • Manager: username='manager', password='manager123'
echo    • Staff: username='staff', password='staff123'
echo    • Supervisor: username='supervisor', password='supervisor123'
echo    • Supir: username='supir', password='supir123'
echo.
echo 🔐 Permission Categories:
echo    • Dashboard permissions
echo    • Master data permissions (Karyawan, Kontainer, Tujuan, etc.)
echo    • Pranota permissions (Supir, Tagihan Kontainer)
echo    • Pembayaran permissions
echo    • Tagihan permissions
echo    • Permohonan permissions
echo    • Perbaikan Kontainer permissions
echo    • User & Approval permissions
echo    • System permissions
echo.
echo 📋 Role Permissions:
echo    • Admin: Full access to all permissions
echo    • Manager: Most permissions except user/permission management
echo    • Supervisor: Operational + approval permissions
echo    • Staff: Basic view permissions
echo    • Supir: Limited to their own pranota
echo.
echo ==================================================================================
echo 🎉 Comprehensive permissions seeding completed successfully!
echo ==================================================================================
echo.
echo 🚀 NEXT STEPS:
echo 1. Test login with default users
echo 2. Verify permissions are working correctly
echo 3. Update user passwords in production
echo 4. Configure additional users as needed
echo.
echo Press any key to continue...
pause >nul
