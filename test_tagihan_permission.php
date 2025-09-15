<?php

// Test permission user test4 untuk tagihan kontainer
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== TEST PERMISSION USER TEST4 - TAGIHAN KONTAINER ===\n";

// Cari user test4
$user = User::where('username', 'test4')->first();

if (!$user) {
    echo "❌ User test4 tidak ditemukan!\n";
    exit(1);
}

echo "✅ User test4 ditemukan (ID: {$user->id})\n";
echo "Total permission yang dimiliki: " . $user->permissions->count() . "\n\n";

echo "=== TEST PERMISSION TAGIHAN KONTAINER ===\n";

// Test permission untuk tagihan kontainer
$tagihanPermissions = [
    'tagihan-kontainer-view' => 'View tagihan kontainer',
    'tagihan-kontainer-create' => 'Create tagihan kontainer',
    'tagihan-kontainer-update' => 'Update tagihan kontainer',
    'tagihan-kontainer-delete' => 'Delete tagihan kontainer',
];

$allTestsPass = true;

foreach ($tagihanPermissions as $permission => $description) {
    $hasPermission = $user->can($permission);
    $status = $hasPermission ? '✅ YES' : '❌ NO';
    echo "{$permission}: {$status} - {$description}\n";

    if ($permission === 'tagihan-kontainer-view' && !$hasPermission) {
        $allTestsPass = false;
    }
}

echo "\n=== VERIFIKASI SIDEBAR CHECK ===\n";

// Simulasi pengecekan sidebar
$sidebarCheck = $user->can('tagihan-kontainer-view');
$status = $sidebarCheck ? '✅ AKAN MUNCUL' : '❌ TIDAK AKAN MUNCUL';
echo "Sidebar check (tagihan-kontainer-view): {$status}\n";

echo "\n=== RINGKASAN ===\n";
if ($allTestsPass) {
    echo "✅ User test4 memiliki permission yang benar untuk melihat menu tagihan kontainer\n";
    echo "✅ Menu tagihan kontainer seharusnya muncul di sidebar\n";
} else {
    echo "❌ User test4 tidak memiliki permission tagihan-kontainer-view\n";
    echo "❌ Menu tagihan kontainer tidak akan muncul di sidebar\n";
}

echo "\n=== PERMISSION YANG DIMILIKI USER TEST4 ===\n";
$userPermissions = $user->permissions->pluck('name')->toArray();
$tagihanRelated = array_filter($userPermissions, function($perm) {
    return strpos($perm, 'tagihan') !== false;
});

if (count($tagihanRelated) > 0) {
    echo "Permission terkait tagihan:\n";
    foreach ($tagihanRelated as $perm) {
        echo "- {$perm}\n";
    }
} else {
    echo "Tidak ada permission terkait tagihan yang dimiliki user test4\n";
}

echo "\nTest selesai.\n";
