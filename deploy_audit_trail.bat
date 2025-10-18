@echo off
REM Script deployment audit trail system untuk Windows Server
REM File: deploy_audit_trail.bat

echo.
echo 🚀 DEPLOYING AUDIT TRAIL SYSTEM TO WINDOWS SERVER
echo ==================================================
echo Started at: %date% %time%
echo.

REM Check if we're in Laravel project directory
if not exist "artisan" (
    echo ❌ File 'artisan' tidak ditemukan. Pastikan Anda berada di direktori Laravel project.
    pause
    exit /b 1
)

echo ℹ️  Detected Laravel project in: %cd%

REM 1. Check PHP version
echo.
echo 📋 CHECKING SYSTEM REQUIREMENTS
echo ================================
php -r "echo 'PHP Version: ' . PHP_VERSION . PHP_EOL;"

REM 2. Run migration
echo.
echo 📊 RUNNING DATABASE MIGRATION
echo =============================
php artisan migrate --force
if %errorlevel% neq 0 (
    echo ❌ Migration gagal! Periksa konfigurasi database.
    pause
    exit /b 1
)
echo ✅ Migration berhasil dijalankan

REM 3. Setup permissions
echo.
echo 🔑 SETTING UP PERMISSIONS
echo =========================
if exist "setup_audit_permissions_server.php" (
    php setup_audit_permissions_server.php
    if %errorlevel% equ 0 (
        echo ✅ Permissions setup berhasil
    ) else (
        echo ⚠️  Permissions setup ada masalah, silakan cek manual
    )
) else (
    echo ⚠️  File setup_audit_permissions_server.php tidak ditemukan
)

REM 4. Add Auditable trait to models
echo.
echo 🏷️  ADDING AUDITABLE TRAIT TO MODELS
echo ===================================
if exist "add_auditable_to_all_models.php" (
    php add_auditable_to_all_models.php
    if %errorlevel% equ 0 (
        echo ✅ Auditable trait berhasil ditambahkan ke models
    ) else (
        echo ⚠️  Ada masalah saat menambahkan Auditable trait
    )
) else (
    echo ⚠️  File add_auditable_to_all_models.php tidak ditemukan
)

REM 5. Clear Laravel caches
echo.
echo 🧹 CLEARING LARAVEL CACHES
echo =========================
php artisan cache:clear
echo ✅ Application cache cleared

php artisan config:clear
echo ✅ Config cache cleared

php artisan route:clear
echo ✅ Route cache cleared

php artisan view:clear
echo ✅ View cache cleared

REM 6. Test implementation
echo.
echo 🧪 TESTING IMPLEMENTATION
echo ========================
if exist "test_audit_log_implementation.php" (
    echo ℹ️  Running audit log tests...
    php test_audit_log_implementation.php
    if %errorlevel% equ 0 (
        echo ✅ Testing berhasil!
    ) else (
        echo ⚠️  Testing ada masalah, silakan cek manual
    )
) else (
    echo ⚠️  File test_audit_log_implementation.php tidak ditemukan
)

REM 7. Check routes
echo.
echo 📍 CHECKING ROUTES
echo =================
echo ℹ️  Checking audit-log routes...
php artisan route:list | findstr /i "audit" >nul 2>&1
if %errorlevel% equ 0 (
    echo ✅ Audit log routes ditemukan
) else (
    echo ⚠️  Audit log routes belum ada. Silakan tambahkan ke routes/web.php
)

REM 8. Final checks
echo.
echo 🔍 FINAL VERIFICATION
echo ===================

REM Check if audit_logs table exists
php -r "require_once 'vendor/autoload.php'; $app = require_once 'bootstrap/app.php'; $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); try { $exists = Schema::hasTable('audit_logs'); echo $exists ? '✅ Table audit_logs sudah ada di database' : '❌ Table audit_logs belum ada di database'; } catch (Exception $e) { echo '⚠️  Tidak dapat memeriksa table audit_logs: ' . $e->getMessage(); }"
echo.

REM Check if files exist
if exist "app\Models\AuditLog.php" (
    echo ✅ AuditLog model file exists
) else (
    echo ❌ AuditLog model file tidak ditemukan
)

if exist "app\Http\Controllers\AuditLogController.php" (
    echo ✅ AuditLogController file exists
) else (
    echo ❌ AuditLogController file tidak ditemukan
)

REM 9. Display completion message
echo.
echo 🎉 DEPLOYMENT COMPLETED!
echo ========================
echo Waktu selesai: %date% %time%
echo.
echo 📋 LANGKAH SELANJUTNYA:
echo 1. ✅ Login ke aplikasi sebagai admin
echo 2. ✅ Pastikan menu 'Audit Log' muncul di sidebar
echo 3. ✅ Test tombol 'Riwayat' di halaman master data
echo 4. ✅ Coba create/update/delete data untuk test audit trail
echo 5. ✅ Periksa dashboard audit log di menu 'Audit Log'
echo.
echo 📞 JIKA ADA MASALAH:
echo - Cek Laravel log di storage\logs\laravel.log
echo - Pastikan database connection berfungsi
echo - Pastikan permissions sudah benar
echo.
echo 🔗 URL UNTUK TESTING:
echo - Dashboard audit log: /audit-logs
echo - Master data pages: /master-* ^(cek tombol Riwayat^)
echo.
echo 💡 TROUBLESHOOTING:
echo - Jika 500 error: php artisan config:clear ^&^& php artisan cache:clear
echo - Jika route not found: tambahkan routes audit log ke routes\web.php
echo.

pause
