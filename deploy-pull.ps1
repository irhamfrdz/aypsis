# PowerShell Deploy Script - Pull Latest Changes from Git
# Script untuk melakukan pull perubahan terbaru dari repository git ke server Windows

Write-Host "🚀 Starting deployment process..." -ForegroundColor Green
Write-Host "=================================" -ForegroundColor Yellow

# 1. Navigasi ke direktori aplikasi
Write-Host "📁 Navigating to application directory..." -ForegroundColor Cyan
$appPath = "C:\inetpub\wwwroot\aypsis"  # Sesuaikan dengan path server Anda
if (Test-Path $appPath) {
    Set-Location $appPath
} else {
    Write-Host "❌ Error: Application directory not found at $appPath!" -ForegroundColor Red
    exit 1
}

# 2. Backup current state (optional)
Write-Host "💾 Creating backup of current state..." -ForegroundColor Cyan
$backupPath = "../aypsis-backup-$(Get-Date -Format 'yyyyMMdd-HHmmss')"
try {
    Copy-Item -Path "." -Destination $backupPath -Recurse -Force
    Write-Host "✅ Backup created at $backupPath" -ForegroundColor Green
} catch {
    Write-Host "⚠️  Backup skipped: $($_.Exception.Message)" -ForegroundColor Yellow
}

# 3. Check current git status
Write-Host "📊 Checking current git status..." -ForegroundColor Cyan
git status

# 4. Stash any local changes
Write-Host "📦 Stashing local changes (if any)..." -ForegroundColor Cyan
git stash

# 5. Pull latest changes from main branch
Write-Host "⬇️  Pulling latest changes from origin/main..." -ForegroundColor Cyan
git pull origin main

# 6. Install/Update composer dependencies
Write-Host "📚 Installing/Updating composer dependencies..." -ForegroundColor Cyan
composer install --optimize-autoloader --no-dev

# 7. Clear application cache
Write-Host "🧹 Clearing application cache..." -ForegroundColor Cyan
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 8. Migrate database (if needed)
Write-Host "🗄️  Running database migrations..." -ForegroundColor Cyan
php artisan migrate --force

# 9. Optimize application
Write-Host "⚡ Optimizing application..." -ForegroundColor Cyan
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 10. Set proper permissions (Windows)
Write-Host "🔐 Setting proper file permissions..." -ForegroundColor Cyan
# Memberikan permission ke IIS_IUSRS untuk direktori storage dan bootstrap/cache
icacls "storage" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "bootstrap/cache" /grant "IIS_IUSRS:(OI)(CI)F" /T

# 11. Restart IIS (if needed)
Write-Host "🔄 Restarting IIS..." -ForegroundColor Cyan
try {
    iisreset /restart
    Write-Host "✅ IIS restarted successfully" -ForegroundColor Green
} catch {
    Write-Host "⚠️  IIS restart skipped: $($_.Exception.Message)" -ForegroundColor Yellow
}

Write-Host "=================================" -ForegroundColor Yellow
Write-Host "✅ Deployment completed successfully!" -ForegroundColor Green
Write-Host "🌐 Application is now updated with latest changes" -ForegroundColor Green
Write-Host "📅 Deployed at: $(Get-Date)" -ForegroundColor Green
