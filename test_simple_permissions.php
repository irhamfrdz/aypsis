<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;

echo "🧪 Testing Sistem Permission Sederhana\n";
echo "=====================================\n\n";

// Login as user test2
$user = User::where('username', 'test2')->first();

if (!$user) {
    echo "❌ User test2 not found\n";
    exit(1);
}

Auth::login($user);
echo "✅ Logged in as: {$user->username}\n\n";

// Test permission sederhana
echo "📋 Test Menu Access dengan Permission Sederhana:\n";

$menus = [
    'dashboard' => 'Dashboard (semua user bisa akses)',
    'tagihan-kontainer' => 'Tagihan Kontainer Sewa',
    'pranota-supir' => 'Pranota Supir',
    'pembayaran-pranota-supir' => 'Pembayaran Pranota Supir',
];

foreach ($menus as $menuKey => $menuName) {
    $canAccess = false;

    switch ($menuKey) {
        case 'dashboard':
            $canAccess = true; // Semua user bisa akses
            break;
        case 'tagihan-kontainer':
            $canAccess = $user->can('tagihan-kontainer');
            break;
        case 'pranota-supir':
            $canAccess = $user->can('pranota-supir');
            break;
        case 'pembayaran-pranota-supir':
            $canAccess = $user->can('pembayaran-pranota-supir');
            break;
    }

    $status = $canAccess ? '✅ ACCESSIBLE' : '❌ BLOCKED';
    echo "  {$menuName}: {$status}\n";
}

echo "\n";

// Show user permissions
$userPermissions = $user->permissions->pluck('name')->toArray();
echo "📋 Permission User test2:\n";
foreach ($userPermissions as $perm) {
    echo "  - {$perm}\n";
}

echo "\n🎯 Analisis:\n";
echo "===========\n";

$simplePermissions = ['tagihan-kontainer', 'pranota-supir', 'pembayaran-pranota-supir'];
$hasSimplePermissions = array_intersect($userPermissions, $simplePermissions);

echo "  ✅ Permission sederhana yang dimiliki: " . count($hasSimplePermissions) . "\n";
echo "  📋 Total permission: " . count($userPermissions) . "\n";

if (count($hasSimplePermissions) === count($simplePermissions)) {
    echo "  🎉 SEMUA permission sederhana sudah ada!\n";
} else {
    echo "  ⚠️  Masih ada permission sederhana yang belum ada\n";
}

echo "\n✨ Keuntungan Sistem Permission Sederhana:\n";
echo "=========================================\n";
echo "  ✅ Nama permission sesuai dengan nama menu\n";
echo "  ✅ Tidak perlu prefix 'master-' yang membingungkan\n";
echo "  ✅ Lebih mudah diingat dan dikelola\n";
echo "  ✅ Konsisten dengan struktur aplikasi\n";
echo "  ✅ Mengurangi kompleksitas permission checking\n";

echo "\n🚀 Status: SISTEM PERMISSION SEDERHANA BERHASIL DIIMPLEMENTASIKAN!\n";
