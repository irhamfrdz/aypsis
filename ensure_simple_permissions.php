<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;

echo "🔧 Memastikan Permission Sederhana Ada di Database\n";
echo "=================================================\n\n";

// Permission sederhana yang diperlukan
$requiredPermissions = [
    'dashboard' => 'Akses halaman dashboard',
    'tagihan-kontainer' => 'Akses menu tagihan kontainer sewa',
    'pranota-supir' => 'Akses menu pranota supir',
    'pembayaran-pranota-supir' => 'Akses menu pembayaran pranota supir',
    'permohonan' => 'Akses menu permohonan memo',
    'user-approval' => 'Persetujuan user baru',
    'master-data' => 'Akses semua menu master data',
    'master-karyawan' => 'Manajemen karyawan',
    'master-user' => 'Manajemen user',
    'master-kontainer' => 'Manajemen kontainer',
    'master-pricelist-sewa-kontainer' => 'Pricelist sewa kontainer',
    'master-tujuan' => 'Manajemen tujuan',
    'master-kegiatan' => 'Manajemen kegiatan',
    'master-permission' => 'Manajemen permission',
    'master-mobil' => 'Manajemen mobil',
];

echo "📋 Mengecek permission yang diperlukan:\n";
$addedPermissions = [];
$existingPermissions = [];

foreach ($requiredPermissions as $permName => $description) {
    $permission = Permission::where('name', $permName)->first();

    if ($permission) {
        echo "  ✅ {$permName} - SUDAH ADA\n";
        $existingPermissions[] = $permName;
    } else {
        // Buat permission baru
        $newPermission = Permission::create([
            'name' => $permName,
            'description' => $description,
        ]);

        echo "  🆕 {$permName} - DITAMBAHKAN\n";
        $addedPermissions[] = $permName;
    }
}

echo "\n📊 Ringkasan:\n";
echo "=============\n";
echo "  ✅ Permission yang sudah ada: " . count($existingPermissions) . "\n";
echo "  🆕 Permission yang ditambahkan: " . count($addedPermissions) . "\n";
echo "  📋 Total permission sekarang: " . count($requiredPermissions) . "\n";

if (!empty($addedPermissions)) {
    echo "\n🎉 Permission baru berhasil ditambahkan:\n";
    foreach ($addedPermissions as $perm) {
        echo "   - {$perm}\n";
    }
} else {
    echo "\n✅ Semua permission sederhana sudah tersedia!\n";
}

echo "\n🚀 Sistem permission sederhana siap digunakan!\n";
echo "\n💡 Permission yang tersedia untuk form create user:\n";
foreach ($requiredPermissions as $name => $desc) {
    echo "   - {$name}: {$desc}\n";
}
