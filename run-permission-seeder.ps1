# ================================================================
# COMPREHENSIVE PERMISSION SEEDER - RUNNER SCRIPT
# ================================================================
# Script untuk menjalankan permission seeder dengan mudah
# Untuk Windows PowerShell
# ================================================================

Write-Host ""
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host "   🔐 COMPREHENSIVE PERMISSION SEEDER - AYPSIS SYSTEM" -ForegroundColor Cyan
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host ""

# Menu pilihan
Write-Host "Pilih opsi yang ingin dijalankan:" -ForegroundColor Yellow
Write-Host ""
Write-Host "1. Seed HANYA Permission (ComprehensivePermissionSeeder)" -ForegroundColor White
Write-Host "2. Seed Permission + Assign ke Admin" -ForegroundColor White
Write-Host "3. Full Seed (Karyawan + Permission + User + Admin)" -ForegroundColor White
Write-Host "4. Migrate Fresh + Full Seed (⚠️  RESET DATABASE)" -ForegroundColor Red
Write-Host "5. Verify Permissions (Check jumlah)" -ForegroundColor White
Write-Host "0. Exit" -ForegroundColor Gray
Write-Host ""

$choice = Read-Host "Masukkan pilihan (0-5)"

function Confirm-Action {
    param([string]$message)
    $response = Read-Host "$message (y/n)"
    return $response -eq 'y' -or $response -eq 'Y' -or $response -eq 'yes' -or $response -eq 'Yes'
}

switch ($choice) {
    "1" {
        Write-Host ""
        Write-Host "🔐 Menjalankan ComprehensivePermissionSeeder..." -ForegroundColor Yellow
        php artisan db:seed --class=ComprehensivePermissionSeeder
        
        if ($LASTEXITCODE -eq 0) {
            Write-Host ""
            Write-Host "✅ Permission seeding berhasil!" -ForegroundColor Green
        } else {
            Write-Host ""
            Write-Host "❌ Error saat seeding permission!" -ForegroundColor Red
        }
    }

    "2" {
        Write-Host ""
        Write-Host "🔐 Menjalankan ComprehensivePermissionSeeder..." -ForegroundColor Yellow
        php artisan db:seed --class=ComprehensivePermissionSeeder

        if ($LASTEXITCODE -eq 0) {
            Write-Host ""
            Write-Host "✅ Permission seeding berhasil!" -ForegroundColor Green
            Write-Host ""
            Write-Host "👤 Assign permission ke admin..." -ForegroundColor Yellow
            php artisan db:seed --class=AdminPermissionSeeder

            if ($LASTEXITCODE -eq 0) {
                Write-Host ""
                Write-Host "✅ Permission berhasil di-assign ke admin!" -ForegroundColor Green
            } else {
                Write-Host ""
                Write-Host "❌ Error saat assign permission ke admin!" -ForegroundColor Red
            }
        } else {
            Write-Host ""
            Write-Host "❌ Error saat seeding permission!" -ForegroundColor Red
        }
    }

    "3" {
        Write-Host ""
        Write-Host "📊 Menjalankan Full Seed..." -ForegroundColor Yellow
        Write-Host ""

        Write-Host "👥 Seeding Karyawan..." -ForegroundColor Yellow
        php artisan db:seed --class=KaryawanSeeder

        Write-Host "🔐 Seeding Permissions..." -ForegroundColor Yellow
        php artisan db:seed --class=ComprehensivePermissionSeeder

        Write-Host "👤 Seeding Users..." -ForegroundColor Yellow
        php artisan db:seed --class=UserSeeder

        Write-Host "✅ Assigning Permissions ke Admin..." -ForegroundColor Yellow
        php artisan db:seed --class=AdminPermissionSeeder

        Write-Host ""
        Write-Host "✅ SELESAI! Full seed berhasil!" -ForegroundColor Green
    }

    "4" {
        if (Confirm-Action "⚠️  WARNING: Ini akan menghapus SEMUA data di database!") {
            Write-Host ""
            Write-Host "🔄 Menjalankan migrate:fresh..." -ForegroundColor Yellow
            php artisan migrate:fresh

            Write-Host "👥 Seeding Karyawan..." -ForegroundColor Yellow
            php artisan db:seed --class=KaryawanSeeder

            Write-Host "🔐 Seeding Permissions..." -ForegroundColor Yellow
            php artisan db:seed --class=ComprehensivePermissionSeeder

            Write-Host "👤 Seeding Users..." -ForegroundColor Yellow
            php artisan db:seed --class=UserSeeder

            Write-Host "✅ Assigning Permissions ke Admin..." -ForegroundColor Yellow
            php artisan db:seed --class=AdminPermissionSeeder

            Write-Host ""
            Write-Host "✅ SELESAI! Migrate fresh + seed berhasil!" -ForegroundColor Green
        } else {
            Write-Host ""
            Write-Host "❌ Dibatalkan oleh user" -ForegroundColor Red
        }
    }

    "5" {
        Write-Host ""
        Write-Host "📊 Mengecek jumlah permissions..." -ForegroundColor Yellow
        Write-Host ""
        
        # Check total permissions
        $totalPerms = php artisan tinker --execute="echo App\Models\Permission::count();"
        Write-Host "Total Permissions di database: $totalPerms" -ForegroundColor Cyan
        
        # Check admin permissions
        Write-Host ""
        Write-Host "📊 Mengecek permissions user admin..." -ForegroundColor Yellow
        $adminPerms = php artisan tinker --execute="`$admin = App\Models\User::find(1); if (`$admin) { echo `$admin->permissions()->count(); } else { echo '0 (Admin not found)'; }"
        Write-Host "Permissions user admin (ID 1): $adminPerms" -ForegroundColor Cyan
        
        Write-Host ""
        Write-Host "ℹ️  Harusnya total permissions = 300+" -ForegroundColor Yellow
    }

    "0" {
        Write-Host ""
        Write-Host "👋 Keluar dari program..." -ForegroundColor Gray
        exit
    }

    default {
        Write-Host ""
        Write-Host "❌ Pilihan tidak valid!" -ForegroundColor Red
        exit
    }
}

Write-Host ""
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host "📝 Informasi Login Default:" -ForegroundColor Cyan
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host "Username: admin" -ForegroundColor White
Write-Host "Password: admin123" -ForegroundColor White
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "Press any key to exit..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
