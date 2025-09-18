@echo off
REM User Admin Seeder Runner Script for Windows
REM This script runs the UserAdminSeeder to create user_admin with all permissions

echo 🚀 Starting User Admin Seeder...
echo ================================

REM Check if we're in the correct directory
if not exist "artisan" (
    echo [ERROR] Artisan file not found. Please run this script from the Laravel project root directory.
    pause
    exit /b 1
)

echo [INFO] Checking Laravel environment...

REM Check if .env file exists
if not exist ".env" (
    echo [WARNING] .env file not found. Please ensure your environment is properly configured.
)

echo.
echo [INFO] Running UserAdminSeeder...
echo ================================

REM Run the UserAdminSeeder
php artisan db:seed --class=UserAdminSeeder

REM Check the exit code
if %ERRORLEVEL% EQU 0 (
    echo.
    echo [SUCCESS] UserAdminSeeder completed successfully!
    echo.
    echo [INFO] User Admin Credentials:
    echo   Username: user_admin
    echo   Password: admin123
    echo   Status: approved (auto-approved)
    echo.
    echo [SUCCESS] The user_admin has been created with ALL permissions!
    echo [WARNING] Remember to change the default password after first login.
) else (
    echo.
    echo [ERROR] UserAdminSeeder failed! Please check the error messages above.
    pause
    exit /b 1
)

echo.
echo [INFO] Verifying user_admin creation...

REM Try to verify the user was created using artisan tinker
php artisan tinker --execute="echo App\Models\User::where('username', 'user_admin')->count();" > temp_verify.txt 2>nul
if exist temp_verify.txt (
    set /p USER_COUNT=<temp_verify.txt
    del temp_verify.txt
    if "%USER_COUNT%"=="1" (
        echo [SUCCESS] Verification successful: user_admin created!
    ) else (
        echo [WARNING] Could not verify user creation automatically.
    )
) else (
    echo [WARNING] Could not verify user creation automatically.
)

echo.
echo [SUCCESS] 🎉 User Admin Seeder process completed!
echo [INFO] You can now login with user_admin / admin123
echo.
pause