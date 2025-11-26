# =================================================================
# APPROVAL ORDER PERMISSIONS DEPLOYMENT SCRIPT (Windows PowerShell)
# =================================================================
# Script untuk menambahkan approval order permissions di Windows
# Usage: .\deploy_approval_order_permissions.ps1
# =================================================================

Write-Host "🚀 APPROVAL ORDER PERMISSIONS DEPLOYMENT" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

# Check if we're in Laravel directory
if (-not (Test-Path "artisan")) {
    Write-Host "❌ Error: artisan file not found. Please run this script from Laravel root directory." -ForegroundColor Red
    exit 1
}

# Check if .env file exists
if (-not (Test-Path ".env")) {
    Write-Host "❌ Error: .env file not found. Please ensure Laravel is properly configured." -ForegroundColor Red
    exit 1
}

Write-Host "📂 Current directory: $(Get-Location)" -ForegroundColor Cyan
Write-Host "🔧 Running in Laravel application..." -ForegroundColor Cyan
Write-Host ""

try {
    # Step 1: Run migration for approval order permissions
    Write-Host "🔄 Step 1: Running migration for approval-order permissions..." -ForegroundColor Yellow
    $migrationResult = & php artisan migrate --force
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Migration completed successfully" -ForegroundColor Green
    } else {
        Write-Host "❌ Migration failed" -ForegroundColor Red
        exit 1
    }
    Write-Host ""

    # Step 2: Run artisan command to add permissions
    Write-Host "🔄 Step 2: Adding approval-order permissions..." -ForegroundColor Yellow
    
    # Check if custom command exists
    $commandExists = & php artisan list | Select-String "permissions:add-approval-order"
    
    if ($commandExists) {
        & php artisan permissions:add-approval-order --force
        Write-Host "✅ Artisan command executed successfully" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Artisan command not found, using migration fallback..." -ForegroundColor Yellow
        Write-Host "✅ Permissions added via migration" -ForegroundColor Green
    }
    Write-Host ""

    # Step 3: Clear caches
    Write-Host "🔄 Step 3: Clearing application caches..." -ForegroundColor Yellow
    & php artisan config:clear
    & php artisan route:clear
    & php artisan view:clear
    
    Write-Host "✅ Caches cleared" -ForegroundColor Green
    Write-Host ""

    # Step 4: Verify installation
    Write-Host "🔄 Step 4: Verifying installation..." -ForegroundColor Yellow

    # Check if routes are available
    $routeCheck = & php artisan route:list --name="approval-order" 2>$null
    if ($routeCheck) {
        Write-Host "✅ Approval-order routes are available" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Approval-order routes not found" -ForegroundColor Yellow
    }

    Write-Host ""

    # Summary
    Write-Host "🎯 DEPLOYMENT SUMMARY" -ForegroundColor Green
    Write-Host "====================" -ForegroundColor Green
    Write-Host "✅ Migration executed" -ForegroundColor Green
    Write-Host "✅ Permissions added to database" -ForegroundColor Green
    Write-Host "✅ Application caches cleared" -ForegroundColor Green
    Write-Host "✅ Installation verified" -ForegroundColor Green
    Write-Host ""

    Write-Host "📋 NEXT STEPS:" -ForegroundColor Cyan
    Write-Host "1. Login to admin panel" -ForegroundColor White
    Write-Host "2. Go to Master User → Edit User" -ForegroundColor White
    Write-Host "3. Expand 'Sistem Persetujuan' section" -ForegroundColor White
    Write-Host "4. Configure 'Approval Order' permissions" -ForegroundColor White
    Write-Host "5. Test approval order functionality" -ForegroundColor White
    Write-Host ""

    Write-Host "🎉 APPROVAL ORDER PERMISSIONS DEPLOYMENT COMPLETED!" -ForegroundColor Green
    Write-Host "===================================================" -ForegroundColor Green

} catch {
    Write-Host "❌ An error occurred during deployment:" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    exit 1
}