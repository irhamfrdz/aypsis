@echo off
set COMPOSER_DISABLE_NETWORK=0
set COMPOSER_DISABLE_TLS=1
set COMPOSER_SECURE_HTTP=0
composer install --no-dev --optimize-autoloader
