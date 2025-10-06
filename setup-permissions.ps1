# ========================================
# 🚀 AYPSIS - Permission Seeder Setup
# ========================================
# Script untuk setup lengkap permission system
# Jalankan dengan: .\setup-permissions.ps1

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "🔐 AYPSIS Permission Seeder Setup" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Function untuk konfirmasi
function Confirm-Action {
    param([string]$Message)
    Write-Host "$Message" -ForegroundColor Yellow
    $response = Read-Host "Lanjutkan? (y/n)"
    return $response -eq 'y'
}

# Menu pilihan
Write-Host "Pilih opsi setup:" -ForegroundColor Green
Write-Host "1. Fresh Install (Drop semua data & migrate ulang)" -ForegroundColor White
Write-Host "2. Seed Permission Saja (Update permissions)" -ForegroundColor White
Write-Host "3. Seed Permission + Assign ke Admin" -ForegroundColor White
Write-Host "4. Full Seed (Karyawan + User + Permission + Admin)" -ForegroundColor White
Write-Host ""

$choice = Read-Host "Masukkan pilihan (1-4)"

switch ($choice) {
    "1" {
        if (Confirm-Action "⚠️  WARNING: Ini akan menghapus SEMUA data di database!") {
            Write-Host ""
            Write-Host "🔄 Menjalankan migrate:fresh..." -ForegroundColor Yellow
            php artisan migrate:fresh

            Write-Host "📊 Seeding Karyawan..." -ForegroundColor Yellow
            php artisan db:seed --class=KaryawanSeeder

            Write-Host "👥 Seeding Users..." -ForegroundColor Yellow
            php artisan db:seed --class=UserSeeder

            Write-Host "🔐 Seeding Permissions..." -ForegroundColor Yellow
            php artisan db:seed --class=PermissionSeederComprehensive

            Write-Host "✅ Assigning Permissions ke Admin..." -ForegroundColor Yellow
            php artisan db:seed --class=AdminPermissionSeeder

            Write-Host ""
            Write-Host "✅ SELESAI! Fresh install berhasil!" -ForegroundColor Green
        } else {
            Write-Host "❌ Setup dibatalkan" -ForegroundColor Red
        }
    }

    "2" {
        Write-Host ""
        Write-Host "🔐 Seeding Permissions..." -ForegroundColor Yellow
        php artisan db:seed --class=PermissionSeederComprehensive

        Write-Host ""
        Write-Host "✅ SELESAI! Permissions berhasil di-update!" -ForegroundColor Green
    }

    "3" {
        Write-Host ""
        Write-Host "🔐 Seeding Permissions..." -ForegroundColor Yellow
        php artisan db:seed --class=PermissionSeederComprehensive

        Write-Host "✅ Assigning Permissions ke Admin..." -ForegroundColor Yellow
        php artisan db:seed --class=AdminPermissionSeeder

        Write-Host ""
        Write-Host "✅ SELESAI! Permissions di-update & assigned ke admin!" -ForegroundColor Green
    }

    "4" {
        Write-Host ""
        Write-Host "📊 Seeding Karyawan..." -ForegroundColor Yellow
        php artisan db:seed --class=KaryawanSeeder

        Write-Host "👥 Seeding Users..." -ForegroundColor Yellow
        php artisan db:seed --class=UserSeeder

        Write-Host "🔐 Seeding Permissions..." -ForegroundColor Yellow
        php artisan db:seed --class=PermissionSeederComprehensive

        Write-Host "✅ Assigning Permissions ke Admin..." -ForegroundColor Yellow
        php artisan db:seed --class=AdminPermissionSeeder

        Write-Host ""
        Write-Host "✅ SELESAI! Full seed berhasil!" -ForegroundColor Green
    }

    default {
        Write-Host "❌ Pilihan tidak valid!" -ForegroundColor Red
        exit
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "📝 Informasi Login Default:" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Username: admin" -ForegroundColor White
Write-Host "Password: admin123" -ForegroundColor White
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Verifikasi
$verify = Read-Host "Verifikasi jumlah permissions? (y/n)"
if ($verify -eq 'y') {
    Write-Host ""
    Write-Host "🔍 Memeriksa jumlah permissions..." -ForegroundColor Yellow
    php artisan tinker --execute="echo 'Total Permissions: ' . \App\Models\Permission::count(); echo PHP_EOL; echo 'Admin Permissions: ' . \App\Models\User::find(1)->permissions()->count();"
}

Write-Host ""
Write-Host "✨ Setup selesai! Selamat bekerja dengan AYPSIS!" -ForegroundColor Green
