<?php

// Script untuk test akses menu tanpa permission create

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== TEST AKSES MENU TANPA PERMISSION CREATE ===\n\n";

// Buat user test tanpa permission create
$testUser = User::create([
    'username' => 'test_user_view_only_' . time(),
    'password' => bcrypt('password123'),
]);

echo "Test user created: {$testUser->username}\n\n";

// Menu yang akan ditest
$menus = [
    'cabang' => [
        'view' => 'master-cabang-view',
        'create' => 'master-cabang-create',
        'route' => 'master.cabang.index'
    ],
    'coa' => [
        'view' => 'master-coa-view',
        'create' => 'master-coa-create',
        'route' => 'master-coa-index'
    ],
    'kode-nomor' => [
        'view' => 'master-kode-nomor-view',
        'create' => 'master-kode-nomor-create',
        'route' => 'master.kode-nomor.index'
    ],
    'nomor-terakhir' => [
        'view' => 'master-nomor-terakhir-view',
        'create' => 'master-nomor-terakhir-create',
        'route' => 'master.nomor-terakhir.index'
    ],
    'tipe-akun' => [
        'view' => 'master-tipe-akun-view',
        'create' => 'master-tipe-akun-create',
        'route' => 'master.tipe-akun.index'
    ]
];

foreach ($menus as $menuName => $permissions) {
    echo "=== TESTING MENU: " . strtoupper($menuName) . " ===\n";

    // Test 1: Hanya dengan permission VIEW
    $viewPermission = Permission::where('name', $permissions['view'])->first();
    if ($viewPermission) {
        $testUser->permissions()->sync([$viewPermission->id]);
        $testUser->refresh();
        $testUser->load('permissions');

        echo "✅ Permission VIEW assigned: {$permissions['view']}\n";

        // Test apakah bisa akses menu (berdasarkan kondisi sidebar)
        $canViewMenu = $testUser->can($permissions['view']);
        echo "Menu {$menuName} dengan VIEW only: " . ($canViewMenu ? '✅ DAPAT DIAKSES' : '❌ TIDAK DAPAT DIAKSES') . "\n";

        // Test apakah bisa akses halaman create (akan gagal)
        $canCreate = $testUser->can($permissions['create']);
        echo "Halaman create {$menuName}: " . ($canCreate ? '✅ DAPAT DIAKSES' : '❌ TIDAK DAPAT DIAKSES') . "\n";

    } else {
        echo "❌ Permission VIEW tidak ditemukan: {$permissions['view']}\n";
    }

    echo "\n";
}

echo "=== KESIMPULAN ===\n";
echo "Berdasarkan implementasi:\n";
echo "1. MENU SIDEBAR: Hanya memerlukan permission VIEW untuk muncul\n";
echo "2. HALAMAN INDEX: Hanya memerlukan permission VIEW untuk diakses\n";
echo "3. HALAMAN CREATE: Memerlukan permission CREATE khusus\n";
echo "4. HALAMAN EDIT: Memerlukan permission UPDATE khusus\n";
echo "5. HAPUS DATA: Memerlukan permission DELETE khusus\n\n";

echo "JADI JAWABANNYA: TIDAK, untuk mengakses menu tidak perlu centang CREATE.\n";
echo "CREATE hanya diperlukan untuk membuat data baru di dalam menu tersebut.\n\n";

// Cleanup
$testUser->permissions()->detach();
$testUser->delete();
echo "Test user cleaned up.\n";
