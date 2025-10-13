<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 DEBUG SIDEBAR CONDITIONS - TUJUAN KIRIM\n";
echo "==========================================\n\n";

// Simulate user login
$adminUser = \App\Models\User::where('username', 'admin')->first();

if (!$adminUser) {
    echo "❌ Admin user not found!\n";
    exit;
}

// Manually login the user for testing
Auth::login($adminUser);
$user = Auth::user();

echo "👤 Current User: {$user->username} (ID: {$user->id})\n\n";

echo "📋 PERMISSION CHECKS:\n";
echo "====================\n";

// Check specific permission
$hasPermission = $user->can('master-tujuan-kirim-view');
echo "1. master-tujuan-kirim-view: " . ($hasPermission ? "✅ YES" : "❌ NO") . "\n";

// Check all tujuan-kirim permissions
$allPerms = $user->getAllPermissions()->pluck('name')->toArray();
$tujuanKirimPerms = array_filter($allPerms, function($perm) {
    return strpos($perm, 'tujuan-kirim') !== false;
});

if (!empty($tujuanKirimPerms)) {
    echo "2. All tujuan-kirim permissions: " . implode(', ', $tujuanKirimPerms) . "\n";
} else {
    echo "2. ❌ NO tujuan-kirim permissions found\n";
}

echo "\n🎯 SIDEBAR CONDITIONS:\n";
echo "=====================\n";

// Check if user is admin
$isAdmin = $user && ($user->username === 'admin' || $user->hasRole('admin'));
echo "3. Is Admin: " . ($isAdmin ? "✅ YES" : "❌ NO") . "\n";

// Check hasMasterPermissions condition (copy from blade)
$hasMasterPermissions = $user && (
    $user->can('master-permission-view') ||
    $user->can('master-cabang-view') ||
    $user->can('master-pengirim-view') ||
    $user->can('master-jenis-barang-view') ||
    $user->can('master-term-view') ||
    $user->can('master-coa-view') ||
    $user->can('master-kode-nomor-view') ||
    $user->can('master-nomor-terakhir-view') ||
    $user->can('master-tipe-akun-view') ||
    $user->can('master-tujuan-view') ||
    $user->can('master-tujuan-kirim-view') ||
    $user->can('master-kegiatan-view')
);

echo "4. Has Master Permissions: " . ($hasMasterPermissions ? "✅ YES" : "❌ NO") . "\n";

// Check showMasterSection condition (copy from blade)  
$showMasterSection = $isAdmin || $hasMasterPermissions;
echo "5. Show Master Section: " . ($showMasterSection ? "✅ YES" : "❌ NO") . "\n";

// Check specific tujuan-kirim condition
$canViewTujuanKirim = $user && $user->can('master-tujuan-kirim-view');
echo "6. Can View Tujuan Kirim: " . ($canViewTujuanKirim ? "✅ YES" : "❌ NO") . "\n";

echo "\n🛣️ ROUTE CHECKS:\n";
echo "===============\n";

// Check route exists
$routeExists = Route::has('tujuan-kirim.index');
echo "7. Route exists: " . ($routeExists ? "✅ YES" : "❌ NO") . "\n";

if ($routeExists) {
    try {
        $routeUrl = route('tujuan-kirim.index');
        echo "8. Route URL: {$routeUrl}\n";
    } catch (Exception $e) {
        echo "8. ❌ Error generating route URL: " . $e->getMessage() . "\n";
    }
}

echo "\n🎨 UI CONDITIONS SUMMARY:\n";
echo "=========================\n";

echo "Menu 'Master Data' akan muncul jika: " . ($showMasterSection ? "✅ TERPENUHI" : "❌ TIDAK TERPENUHI") . "\n";
echo "Menu 'Tujuan Kirim' akan muncul jika: " . ($canViewTujuanKirim ? "✅ TERPENUHI" : "❌ TIDAK TERPENUHI") . "\n";

if ($showMasterSection && $canViewTujuanKirim && $routeExists) {
    echo "\n🎉 SEMUA KONDISI TERPENUHI!\n";
    echo "Menu seharusnya sudah muncul di sidebar.\n\n";
    echo "💡 TROUBLESHOOTING:\n";
    echo "===================\n";
    echo "1. Buka browser dalam mode Incognito/Private\n";
    echo "2. Atau clear semua cache browser (Ctrl+Shift+Delete)\n";
    echo "3. Atau tekan F12 → Application tab → Clear Storage → Clear site data\n";
    echo "4. Login ulang sebagai admin\n";
    echo "5. Periksa di sidebar bagian 'Master Data'\n";
} else {
    echo "\n❌ ADA KONDISI YANG TIDAK TERPENUHI\n";
    echo "Silakan periksa item yang bertanda ❌ di atas.\n";
}

// Logout user
Auth::logout();