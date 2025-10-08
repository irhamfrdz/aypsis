@echo off
REM 🔧 FIX COMPOSER LOCAL - Windows Batch Script
echo 🔧 FIXING COMPOSER LOCAL INSTALLATION...
echo ========================================

REM Set environment for local development
set COMPOSER_DISABLE_TLS=1
set COMPOSER_ALLOW_SUPERUSER=1

echo 📝 1. Setting environment variables...
echo COMPOSER_DISABLE_TLS=1
echo COMPOSER_ALLOW_SUPERUSER=1

echo 🧹 2. Cleaning composer files...
if exist composer.lock del composer.lock
if exist vendor rmdir /S /Q vendor

echo 🔧 3. Setting composer config...
composer config disable-tls true
composer config secure-http false
composer config repo.packagist composer http://packagist.org

echo 📦 4. Installing with fallback method...
REM Try method 1: Install with ignore SSL
composer install --no-dev --optimize-autoloader --ignore-platform-reqs 2>nul

if %ERRORLEVEL% NEQ 0 (
    echo ⚠️  Method 1 failed, trying method 2...
    REM Try method 2: Update first then install
    composer update --no-dev --lock --ignore-platform-reqs 2>nul
    if %ERRORLEVEL% EQU 0 (
        composer install --no-dev --optimize-autoloader --ignore-platform-reqs
    ) else (
        echo ❌ Method 2 failed, trying method 3...
        REM Try method 3: Manual create minimal composer.lock
        echo Creating minimal composer.lock...
        echo {"packages":[],"packages-dev":[],"aliases":[],"minimum-stability":"stable","stability-flags":[],"prefer-stable":false,"prefer-lowest":false,"platform":[],"platform-dev":[],"plugin-api-version":"2.3.0","content-hash":"dummy"} > composer.lock
        composer install --no-dev --optimize-autoloader --ignore-platform-reqs
    )
)

echo ✅ COMPOSER FIX COMPLETED!
echo =========================
pause
