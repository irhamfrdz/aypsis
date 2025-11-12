@echo off
REM Batch script to restore daftar_tagihan_kontainer_sewa from aypsis1.sql

echo ========================================
echo RESTORE DATABASE FROM SQL FILE
echo ========================================
echo.

REM Backup existing data first
echo [1/4] Creating backup of existing data...
php -r "echo date('Y-m-d H:i:s');" > backup_timestamp.txt
set /p TIMESTAMP=<backup_timestamp.txt
del backup_timestamp.txt

echo Backup timestamp: %TIMESTAMP%

REM Get database credentials from Laravel
echo.
echo [2/4] Reading database credentials...
php -r "require 'vendor/autoload.php'; $app = require_once 'bootstrap/app.php'; $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); echo env('DB_HOST').' '.env('DB_DATABASE').' '.env('DB_USERNAME').' '.env('DB_PASSWORD');" > db_config.txt
set /p DB_CONFIG=<db_config.txt
del db_config.txt

REM Parse credentials
for /f "tokens=1,2,3,4" %%a in ("%DB_CONFIG%") do (
    set DB_HOST=%%a
    set DB_DATABASE=%%b
    set DB_USERNAME=%%c
    set DB_PASSWORD=%%d
)

echo Database: %DB_DATABASE%
echo Host: %DB_HOST%
echo.

REM Truncate table
echo [3/4] Truncating daftar_tagihan_kontainer_sewa table...
mysql -h%DB_HOST% -u%DB_USERNAME% -p%DB_PASSWORD% %DB_DATABASE% -e "SET FOREIGN_KEY_CHECKS=0; TRUNCATE TABLE daftar_tagihan_kontainer_sewa; SET FOREIGN_KEY_CHECKS=1;"

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to truncate table
    pause
    exit /b 1
)

echo Table truncated successfully
echo.

REM Extract and restore data
echo [4/4] Restoring data from aypsis1.sql...
echo This may take a few minutes...

REM Use grep to extract only daftar_tagihan_kontainer_sewa INSERT statements
findstr /C:"INSERT INTO `daftar_tagihan_kontainer_sewa`" aypsis1.sql > temp_insert.sql

REM Import the extracted data
mysql -h%DB_HOST% -u%DB_USERNAME% -p%DB_PASSWORD% %DB_DATABASE% < temp_insert.sql

if %ERRORLEVEL% NEQ 0 (
    echo ERROR: Failed to restore data
    del temp_insert.sql
    pause
    exit /b 1
)

del temp_insert.sql

echo.
echo ========================================
echo RESTORE COMPLETED SUCCESSFULLY!
echo ========================================
echo.

REM Verify data
echo Verifying restored data...
mysql -h%DB_HOST% -u%DB_USERNAME% -p%DB_PASSWORD% %DB_DATABASE% -e "SELECT COUNT(*) as total_records FROM daftar_tagihan_kontainer_sewa;"

echo.
echo Sample data (first 3 records):
mysql -h%DB_HOST% -u%DB_USERNAME% -p%DB_PASSWORD% %DB_DATABASE% -e "SELECT id, nomor_kontainer, size, masa, tarif, dpp, ppn, grand_total FROM daftar_tagihan_kontainer_sewa ORDER BY id LIMIT 3;"

echo.
echo Done!
pause
