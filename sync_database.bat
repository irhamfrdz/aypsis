@echo off
REM Database Synchronization Script for Windows
REM This script will sync server database with laptop database

echo 🚀 Starting Database Synchronization...
echo ======================================

REM Colors for Windows (limited support)
REM Using simple text for Windows compatibility

echo.
echo [WARNING] IMPORTANT: Make sure you have backed up your database before proceeding!
echo.
set /p backup="Have you backed up your database? (y/N): "
if /i not "%backup%"=="y" (
    echo [ERROR] Please backup your database first, then run this script again.
    pause
    exit /b 1
)

REM Check if we're in the right directory
if not exist "artisan" (
    echo [ERROR] Please run this script from the Laravel project root directory
    pause
    exit /b 1
)

REM Check database connection
echo.
echo [INFO] Checking database connection...
php artisan migrate:status >nul 2>&1
if %errorlevel% neq 0 (
    echo [ERROR] Cannot connect to database. Please check your .env file.
    pause
    exit /b 1
)
echo [SUCCESS] Database connection OK

REM Run the synchronization seeder
echo.
echo [INFO] Starting database synchronization...
echo.

php artisan db:seed --class=DatabaseSyncSeeder

if %errorlevel% equ 0 (
    echo.
    echo [SUCCESS] Database synchronization completed successfully!
    echo.
    echo [INFO] Verifying synchronization...

    REM Count records using PowerShell
    for /f %%i in ('powershell -command "(php artisan tinker --execute='echo DB::table(''permissions'')->count();' | Select-Object -Last 1)"') do set PERMISSIONS_COUNT=%%i
    for /f %%i in ('powershell -command "(php artisan tinker --execute='echo DB::table(''users'')->count();' | Select-Object -Last 1)"') do set USERS_COUNT=%%i
    for /f %%i in ('powershell -command "(php artisan tinker --execute='echo DB::table(''user_permissions'')->count();' | Select-Object -Last 1)"') do set USER_PERMISSIONS_COUNT=%%i
    for /f %%i in ('powershell -command "(php artisan tinker --execute='echo DB::table(''user_permissions'')->where(''user_id'', 1)->count();' | Select-Object -Last 1)"') do set ADMIN_PERMISSIONS=%%i

    echo.
    echo [SUCCESS] Synchronization Summary:
    echo   📊 Permissions: %PERMISSIONS_COUNT% (should be 381)
    echo   👥 Users: %USERS_COUNT% (should be 7)
    echo   🔐 User Permissions: %USER_PERMISSIONS_COUNT%
    echo   👑 Admin Permissions: %ADMIN_PERMISSIONS% (should be 381)

    echo.
    echo [SUCCESS] 🎉 Database is now synced with laptop database!
    echo [INFO] You can now test the application with the updated permissions.

) else (
    echo.
    echo [ERROR] Database synchronization failed!
    echo [INFO] Please check the error messages above and try again.
)

echo.
pause
