@echo off
echo Aktivasi Extension PHP untuk Laravel...
echo.

REM Backup php.ini terlebih dahulu
copy "C:\xampp\php\php.ini" "C:\xampp\php\php.ini.backup"
echo Backup php.ini dibuat...

REM Aktifkan extension yang dibutuhkan
powershell -Command "(gc 'C:\xampp\php\php.ini') -replace ';extension=gd', 'extension=gd' | Out-File -encoding ASCII 'C:\xampp\php\php.ini'"
powershell -Command "(gc 'C:\xampp\php\php.ini') -replace ';extension=zip', 'extension=zip' | Out-File -encoding ASCII 'C:\xampp\php\php.ini'"
powershell -Command "(gc 'C:\xampp\php\php.ini') -replace ';extension=fileinfo', 'extension=fileinfo' | Out-File -encoding ASCII 'C:\xampp\php\php.ini'"

echo Extension diaktifkan...
echo.
echo Silakan restart Apache dari XAMPP Control Panel
echo Lalu jalankan: php -m | findstr -i "zip gd"
echo.
pause