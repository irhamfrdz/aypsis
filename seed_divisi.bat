@echo off
REM Divisi Seeder Runner Script for Windows
REM Usage: seed_divisi.bat

echo ==========================================
echo    Master Divisi Seeder Runner
echo ==========================================
echo.

REM Check if we're in the right directory
if not exist "artisan" (
    echo ❌ Error: artisan file not found. Please run this script from Laravel project root.
    pause
    exit /b 1
)

REM Check if DivisiSeeder exists
if not exist "database\seeders\DivisiSeeder.php" (
    echo ❌ Error: DivisiSeeder.php not found in database\seeders\
    pause
    exit /b 1
)

echo ✅ Laravel project detected
echo ✅ DivisiSeeder found
echo.

echo 🔄 Running Master Divisi Seeder...
echo Command: php artisan db:seed --class=DivisiSeeder
echo.

REM Run the seeder
php artisan db:seed --class=DivisiSeeder

if %errorlevel% equ 0 (
    echo.
    echo ✅ Master Divisi Seeder completed successfully!
    echo.
    echo ==========================================
    echo    Seeder Execution Summary
    echo ==========================================
    echo ✅ Master Divisi data has been seeded
    echo.
    echo Next steps:
    echo 1. Check the web interface: Master → Divisi
    echo 2. Verify data in database:
    echo    php artisan tinker
    echo    App\Models\Divisi::count()
    echo.
    echo ==========================================
) else (
    echo.
    echo ❌ Error running Master Divisi Seeder
    echo Please check the error messages above
)

echo.
pause
