<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "=== ANALISIS JUMLAH PERMISSION DI DATABASE ===\n";

$totalPermissions = Permission::count();
echo "Total permission: $totalPermissions\n\n";

echo "=== PERMISSION BERDASARKAN KATEGORI ===\n";

// Permission dengan master-
$masterPerms = Permission::where('name', 'like', 'master-%')->count();
echo "Master permissions: $masterPerms\n";

// Permission dengan tagihan-
$tagihanPerms = Permission::where('name', 'like', 'tagihan-%')->count();
echo "Tagihan permissions: $tagihanPerms\n";

// Permission dengan pranota-
$pranotaPerms = Permission::where('name', 'like', 'pranota-%')->count();
echo "Pranota permissions: $pranotaPerms\n";

// Permission dengan pembayaran-
$pembayaranPerms = Permission::where('name', 'like', 'pembayaran-%')->count();
echo "Pembayaran permissions: $pembayaranPerms\n";

// Permission dengan perbaikan-
$perbaikanPerms = Permission::where('name', 'like', 'perbaikan-%')->count();
echo "Perbaikan permissions: $perbaikanPerms\n";

// Permission dengan admin-
$adminPerms = Permission::where('name', 'like', 'admin-%')->count();
echo "Admin permissions: $adminPerms\n";

// Permission dengan profile-
$profilePerms = Permission::where('name', 'like', 'profile-%')->count();
echo "Profile permissions: $profilePerms\n";

// Permission dengan supir-
$supirPerms = Permission::where('name', 'like', 'supir-%')->count();
echo "Supir permissions: $supirPerms\n";

// Permission dengan approval-
$approvalPerms = Permission::where('name', 'like', 'approval-%')->count();
echo "Approval permissions: $approvalPerms\n";

// Permission dengan permohonan-
$permohonanPerms = Permission::where('name', 'like', 'permohonan-%')->count();
echo "Permohonan permissions: $permohonanPerms\n";

// Permission dengan user-
$userPerms = Permission::where('name', 'like', 'user-%')->count();
echo "User permissions: $userPerms\n";

// Permission dengan daftar-
$daftarPerms = Permission::where('name', 'like', 'daftar-%')->count();
echo "Daftar permissions: $daftarPerms\n";

// Permission lainnya
$otherPerms = Permission::where('name', 'not like', 'master-%')
    ->where('name', 'not like', 'tagihan-%')
    ->where('name', 'not like', 'pranota-%')
    ->where('name', 'not like', 'pembayaran-%')
    ->where('name', 'not like', 'perbaikan-%')
    ->where('name', 'not like', 'admin-%')
    ->where('name', 'not like', 'profile-%')
    ->where('name', 'not like', 'supir-%')
    ->where('name', 'not like', 'approval-%')
    ->where('name', 'not like', 'permohonan-%')
    ->where('name', 'not like', 'user-%')
    ->where('name', 'not like', 'daftar-%')
    ->count();
echo "Other permissions: $otherPerms\n\n";

echo "=== CONTOH PERMISSION DARI SETIAP KATEGORI ===\n";

// Ambil contoh dari setiap kategori
$categories = [
    'master' => 'master-%',
    'tagihan' => 'tagihan-%',
    'pranota' => 'pranota-%',
    'pembayaran' => 'pembayaran-%',
    'perbaikan' => 'perbaikan-%',
    'admin' => 'admin-%',
    'profile' => 'profile-%',
    'supir' => 'supir-%',
    'approval' => 'approval-%',
    'permohonan' => 'permohonan-%',
    'user' => 'user-%',
    'daftar' => 'daftar-%'
];

foreach ($categories as $name => $pattern) {
    $example = Permission::where('name', 'like', $pattern)->first();
    if ($example) {
        echo "$name: {$example->name}\n";
    }
}

echo "\n=== PERMISSION YANG PALING BANYAK ===\n";
$topCategories = [
    'master' => Permission::where('name', 'like', 'master-%')->count(),
    'tagihan' => Permission::where('name', 'like', 'tagihan-%')->count(),
    'pranota' => Permission::where('name', 'like', 'pranota-%')->count(),
    'pembayaran' => Permission::where('name', 'like', 'pembayaran-%')->count(),
    'perbaikan' => Permission::where('name', 'like', 'perbaikan-%')->count(),
    'daftar' => Permission::where('name', 'like', 'daftar-%')->count(),
];

arsort($topCategories);
foreach ($topCategories as $category => $count) {
    echo "$category: $count permissions\n";
}
