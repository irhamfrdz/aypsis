<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;

echo "🔧 Sistem Permission Sederhana AYPSIS\n";
echo "===================================\n\n";

// Login as user test2
$user = User::where('username', 'test2')->first();

if (!$user) {
    echo "❌ User test2 not found\n";
    exit(1);
}

Auth::login($user);
echo "✅ Logged in as: {$user->username}\n\n";

// Test permission sederhana
echo "📋 Permission Test untuk Menu:\n";

$menus = [
    'dashboard' => 'Dashboard',
    'master' => 'Master Data',
    'user-approval' => 'Persetujuan User',
    'tagihan-kontainer' => 'Tagihan Kontainer Sewa',
    'permohonan' => 'Permohonan Memo',
    'pranota-supir' => 'Pranota Supir',
    'pembayaran-pranota-supir' => 'Pembayaran Pranota Supir',
];

foreach ($menus as $menuKey => $menuName) {
    $canAccess = false;

    // Logic sederhana untuk cek permission
    switch ($menuKey) {
        case 'dashboard':
            $canAccess = true; // Semua user bisa akses dashboard
            break;
        case 'master':
            $canAccess = $user->can('master-data') ||
                        $user->can('master-karyawan') ||
                        $user->can('master-user') ||
                        (method_exists($user, 'hasPermissionLike') && $user->hasPermissionLike('master-'));
            break;
        case 'user-approval':
            $canAccess = $user->can('master-user') || $user->can('user-approval');
            break;
        case 'tagihan-kontainer':
            $canAccess = $user->can('tagihan-kontainer') ||
                        (method_exists($user, 'hasPermissionLike') && $user->hasPermissionLike('pranota-tagihan-kontainer'));
            break;
        case 'permohonan':
            $canAccess = $user->can('permohonan') ||
                        $user->can('master-permohonan');
            break;
        case 'pranota-supir':
            $canAccess = $user->can('pranota-supir') ||
                        (method_exists($user, 'hasPermissionLike') && $user->hasPermissionLike('pranota-supir'));
            break;
        case 'pembayaran-pranota-supir':
            $canAccess = $user->can('pembayaran-pranota-supir') ||
                        $user->can('master-pembayaran-pranota-supir') ||
                        (method_exists($user, 'hasPermissionLike') && $user->hasPermissionLike('pembayaran-pranota-supir'));
            break;
    }

    $status = $canAccess ? '✅ ACCESSIBLE' : '❌ BLOCKED';
    echo "  {$menuName}: {$status}\n";
}

echo "\n🎯 Rekomendasi Permission untuk User test2:\n";
echo "==========================================\n";

$recommendedPermissions = [
    'tagihan-kontainer' => 'Akses menu Tagihan Kontainer Sewa',
    'pranota-supir' => 'Akses menu Pranota Supir',
    'pembayaran-pranota-supir' => 'Akses menu Pembayaran Pranota Supir',
];

foreach ($recommendedPermissions as $perm => $desc) {
    echo "  ✅ {$perm} - {$desc}\n";
}

echo "\n📝 Cara Implementasi:\n";
echo "===================\n";
echo "1. Tambahkan permission di database:\n";
foreach ($recommendedPermissions as $perm => $desc) {
    echo "   - {$perm}\n";
}
echo "\n2. Update sidebar.blade.php dengan kondisi sederhana:\n";
echo "   - @if(auth()->user()->can('tagihan-kontainer'))\n";
echo "   - @if(auth()->user()->can('pranota-supir'))\n";
echo "   - @if(auth()->user()->can('pembayaran-pranota-supir'))\n";

echo "\n✨ Keuntungan Sistem Baru:\n";
echo "=========================\n";
echo "  ✅ Permission name sesuai dengan menu name\n";
echo "  ✅ Tidak perlu prefix 'master-' yang membingungkan\n";
echo "  ✅ Lebih mudah diingat dan dikelola\n";
echo "  ✅ Konsisten dengan struktur menu\n";

echo "\n🎉 Sistem permission sederhana siap diimplementasikan!\n";
