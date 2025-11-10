# PowerShell Script untuk memperbaiki error migration di Windows Server
# Mengatasi error "Table 'vendor_kontainer_sewas' already exists"

Write-Host "=== Fix Migration Server Script (PowerShell) ===" -ForegroundColor Green
Write-Host "Mengatasi error Table 'vendor_kontainer_sewas' already exists" -ForegroundColor Yellow
Write-Host ""

# Step 1: Cek status migration
Write-Host "1. Checking migration status..." -ForegroundColor Cyan
php artisan migrate:status

Write-Host ""
Write-Host "2. Marking problematic migration as run..." -ForegroundColor Cyan

# Step 2: Tandai migration sebagai sudah dijalankan
try {
    php artisan tinker --execute="DB::table('migrations')->insertOrIgnore(['migration' => '2025_11_08_120000_create_vendor_kontainer_sewas_table', 'batch' => DB::table('migrations')->max('batch') + 1]); echo 'Migration marked as run';"
    Write-Host "✅ Migration marked successfully" -ForegroundColor Green
} catch {
    Write-Host "❌ Error marking migration: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "3. Running remaining migrations..." -ForegroundColor Cyan

# Step 3: Jalankan migration yang tersisa
try {
    php artisan migrate --force
    Write-Host "✅ Migrations completed successfully" -ForegroundColor Green
} catch {
    Write-Host "❌ Error running migrations: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "4. Final migration status..." -ForegroundColor Cyan
php artisan migrate:status

Write-Host ""
Write-Host "=== Migration Fix Complete ===" -ForegroundColor Green
Write-Host "Jika masih ada error, cek manual dengan:" -ForegroundColor Yellow
Write-Host "- php artisan migrate:status" -ForegroundColor White
Write-Host "- php artisan migrate --pretend (untuk preview)" -ForegroundColor White
Write-Host "- php artisan migrate:mark-as-run --migration=nama_migration" -ForegroundColor White