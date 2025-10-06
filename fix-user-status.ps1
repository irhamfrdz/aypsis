# ========================================
# 🔧 Fix User Status - Quick Script
# ========================================
# Script untuk approve semua user yang pending

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "🔧 User Status Fixer" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "Pilih aksi:" -ForegroundColor Green
Write-Host "1. Approve SEMUA user" -ForegroundColor White
Write-Host "2. Approve user tertentu (by username)" -ForegroundColor White
Write-Host "3. Lihat status semua user" -ForegroundColor White
Write-Host ""

$choice = Read-Host "Pilihan (1-3)"

switch ($choice) {
    "1" {
        Write-Host ""
        Write-Host "🔄 Mengapprove semua user..." -ForegroundColor Yellow

        php artisan tinker --execute="DB::table('users')->update(['status' => 'approved']); echo 'All users approved!' . PHP_EOL;"

        Write-Host "✅ Semua user berhasil di-approve!" -ForegroundColor Green
    }

    "2" {
        Write-Host ""
        $username = Read-Host "Masukkan username"

        Write-Host "🔄 Mengapprove user $username..." -ForegroundColor Yellow

        php artisan tinker --execute="DB::table('users')->where('username', '$username')->update(['status' => 'approved']); echo 'User approved!' . PHP_EOL;"

        Write-Host "✅ User $username berhasil di-approve!" -ForegroundColor Green
    }

    "3" {
        Write-Host ""
        Write-Host "📋 Status semua user:" -ForegroundColor Yellow
        Write-Host ""

        # Create temporary PHP script
        $phpScript = @'
<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$users = DB::table('users')->get();
foreach ($users as $user) {
    $statusIcon = $user->status === 'approved' ? '✅' : '⏳';
    echo $statusIcon . " " . str_pad($user->username, 15) . " | Role: " . str_pad($user->role ?? 'N/A', 10) . " | Status: " . $user->status . PHP_EOL;
}
'@

        $phpScript | Out-File -FilePath "temp-check-users.php" -Encoding UTF8
        php temp-check-users.php
        Remove-Item "temp-check-users.php"
    }

    default {
        Write-Host "❌ Pilihan tidak valid!" -ForegroundColor Red
        exit
    }
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "✨ Selesai!" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
