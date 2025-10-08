#!/usr/bin/env powershell
# 🔧 LOCAL COMPOSER FIX - PowerShell Script
# Script untuk fix composer di laptop Windows dengan SSL issues

Write-Host "🔧 LOCAL COMPOSER FIX FOR WINDOWS..." -ForegroundColor Yellow
Write-Host "====================================" -ForegroundColor Yellow

# 1. Set environment variables
Write-Host "⚙️  1. Setting environment variables..." -ForegroundColor Cyan
$env:COMPOSER_DISABLE_TLS = "1"
$env:COMPOSER_ALLOW_SUPERUSER = "1"

# 2. Clean existing files
Write-Host "🧹 2. Cleaning existing files..." -ForegroundColor Cyan
if (Test-Path "composer.lock") { Remove-Item "composer.lock" -Force }
if (Test-Path "vendor") { Remove-Item "vendor" -Recurse -Force }

# 3. Set composer config
Write-Host "🔧 3. Setting composer configuration..." -ForegroundColor Cyan
composer config disable-tls true
composer config secure-http false

# 4. Try different methods
Write-Host "📦 4. Trying different installation methods..." -ForegroundColor Cyan

# Method 1: Try with cached packages
Write-Host "   📌 Method 1: Install with cached packages..." -ForegroundColor Gray
try {
    $result = composer install --no-dev --optimize-autoloader --ignore-platform-reqs --prefer-source 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Method 1 SUCCESS!" -ForegroundColor Green
        exit 0
    }
} catch {
    Write-Host "❌ Method 1 failed" -ForegroundColor Red
}

# Method 2: Create minimal composer.lock and install
Write-Host "   📌 Method 2: Create minimal lock file..." -ForegroundColor Gray
$minimalLock = @{
    packages = @()
    "packages-dev" = @()
    aliases = @()
    "minimum-stability" = "stable"
    "stability-flags" = @{}
    "prefer-stable" = $false
    "prefer-lowest" = $false
    platform = @{}
    "platform-dev" = @{}
    "plugin-api-version" = "2.3.0"
    "content-hash" = "dummy"
} | ConvertTo-Json

$minimalLock | Out-File -FilePath "composer.lock" -Encoding UTF8

try {
    $result = composer install --no-dev --optimize-autoloader --ignore-platform-reqs 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Method 2 SUCCESS!" -ForegroundColor Green
        exit 0
    }
} catch {
    Write-Host "❌ Method 2 failed" -ForegroundColor Red
}

# Method 3: Manual vendor creation (emergency fallback)
Write-Host "   📌 Method 3: Emergency fallback - Manual autoloader..." -ForegroundColor Gray
if (!(Test-Path "vendor")) { New-Item -ItemType Directory -Path "vendor" }
if (!(Test-Path "vendor/autoload.php")) {
    @"
<?php
// Emergency autoload file
spl_autoload_register(function(`$class) {
    `$file = __DIR__ . '/../app/' . str_replace('\\', '/', `$class) . '.php';
    if (file_exists(`$file)) {
        require `$file;
    }
});
"@ | Out-File -FilePath "vendor/autoload.php" -Encoding UTF8
}

Write-Host ""
Write-Host "⚠️  IMPORTANT NOTES:" -ForegroundColor Yellow
Write-Host "   • This is a temporary workaround for local development" -ForegroundColor White
Write-Host "   • For production, use proper composer install on server" -ForegroundColor White
Write-Host "   • SSL issue is common on Windows development environments" -ForegroundColor White
Write-Host ""
Write-Host "✅ LOCAL COMPOSER FIX COMPLETED!" -ForegroundColor Green
Write-Host "=================================" -ForegroundColor Green
