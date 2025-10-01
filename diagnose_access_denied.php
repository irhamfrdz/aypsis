<?php

// Script untuk diagnosa masalah akses ditolak dengan permission view

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== DIAGNOSA MASALAH AKSES DITOLAK ===\n\n";

// Minta input username dari user
echo "Masukkan username yang mengalami masalah: ";
$handle = fopen("php://stdin", "r");
$username = trim(fgets($handle));
fclose($handle);

if (empty($username)) {
    echo "❌ Username tidak boleh kosong!\n";
    exit(1);
}

$user = User::where('username', $username)->with('permissions')->first();

if (!$user) {
    echo "❌ User dengan username '$username' tidak ditemukan!\n";

    // Tampilkan semua user yang ada
    echo "\nUser yang tersedia:\n";
    $users = User::all(['id', 'username']);
    foreach ($users as $u) {
        echo "- {$u->username} (ID: {$u->id})\n";
    }
    exit(1);
}

echo "✅ User ditemukan: {$user->username} (ID: {$user->id})\n";
echo "Total permissions: " . $user->permissions->count() . "\n\n";

// Menu yang sering bermasalah
$menuTests = [
    'Master Cabang' => [
        'permission' => 'master-cabang-view',
        'route' => 'master.cabang.index',
        'url' => '/master/cabang'
    ],
    'Master COA' => [
        'permission' => 'master-coa-view',
        'route' => 'master-coa-index',
        'url' => '/master/coa'
    ],
    'Master Kode Nomor' => [
        'permission' => 'master-kode-nomor-view',
        'route' => 'master.kode-nomor.index',
        'url' => '/master/kode-nomor'
    ],
    'Master Nomor Terakhir' => [
        'permission' => 'master-nomor-terakhir-view',
        'route' => 'master.nomor-terakhir.index',
        'url' => '/master/nomor-terakhir'
    ],
    'Master Tipe Akun' => [
        'permission' => 'master-tipe-akun-view',
        'route' => 'master.tipe-akun.index',
        'url' => '/master/tipe-akun'
    ]
];

echo "=== TESTING PERMISSION UNTUK SETIAP MENU ===\n";

foreach ($menuTests as $menuName => $config) {
    echo "\n📋 $menuName\n";
    echo str_repeat("─", strlen($menuName) + 3) . "\n";

    // Test 1: Cek permission langsung
    $hasExactPermission = $user->permissions->where('name', $config['permission'])->count() > 0;
    echo "1. Permission exact match '{$config['permission']}': " . ($hasExactPermission ? '✅ ADA' : '❌ TIDAK ADA') . "\n";

    // Test 2: Cek dengan method can() (yang sudah diperbaiki)
    $canAccess = $user->can($config['permission']);
    echo "2. Method can() result: " . ($canAccess ? '✅ GRANTED' : '❌ DENIED') . "\n";

    // Test 3: Cek permission alternatif
    $relatedPermissions = $user->permissions->filter(function($perm) use ($config) {
        $menuKey = str_replace('-view', '', $config['permission']);
        return str_contains($perm->name, $menuKey);
    });

    if ($relatedPermissions->count() > 0) {
        echo "3. Permission terkait yang ditemukan:\n";
        foreach ($relatedPermissions as $perm) {
            echo "   - {$perm->name}\n";
        }
    } else {
        echo "3. Tidak ada permission terkait ditemukan\n";
    }

    // Test 4: Diagnosa masalah
    if (!$canAccess) {
        echo "🚨 MASALAH TERDETEKSI untuk menu $menuName:\n";

        if (!$hasExactPermission && $relatedPermissions->count() == 0) {
            echo "   - User tidak memiliki permission apapun untuk menu ini\n";
            echo "   - SOLUSI: Berikan permission '{$config['permission']}' ke user\n";
        } elseif ($hasExactPermission && !$canAccess) {
            echo "   - User punya permission tapi method can() menolak\n";
            echo "   - SOLUSI: Ada bug di method can() atau permission name tidak cocok\n";
        } elseif ($relatedPermissions->count() > 0 && !$canAccess) {
            echo "   - User punya permission terkait tapi tidak yang exact\n";
            echo "   - SOLUSI: Perbaiki nama permission atau tambahkan mapping\n";
        }
    } else {
        echo "✅ Menu $menuName dapat diakses\n";
    }
}

// Cek cache permission (jika ada)
echo "\n=== CEK CACHE DAN SESSION ===\n";
echo "1. User ID: {$user->id}\n";
echo "2. Created at: {$user->created_at}\n";
echo "3. Updated at: {$user->updated_at}\n";

// Saran perbaikan
echo "\n=== SARAN PERBAIKAN ===\n";
echo "1. 🔄 Refresh permission dengan re-login\n";
echo "2. 🛠️  Pastikan permission name exact match\n";
echo "3. 🔍 Cek error log Laravel untuk detail error\n";
echo "4. 🧪 Test dengan user lain yang punya permission sama\n";
echo "5. 📝 Periksa middleware di routes\n\n";

echo "Untuk perbaikan langsung, jalankan:\n";
echo "php fix_user_permissions.php {$username}\n";
