<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$user = User::where('username', 'suci')->first();

if (!$user) {
    echo "User 'suci' tidak ditemukan!\n";
    exit;
}

echo "=== FINAL TEST: User Suci - Master Mobil ===\n\n";
echo "User: {$user->username} (ID: {$user->id})\n\n";

// Test permissions
$permissions = [
    'master-mobil-view',
    'master-mobil-create',
    'master-mobil-update',
    'master-mobil-delete',
];

echo "Permission Check:\n";
foreach ($permissions as $perm) {
    $has = $user->hasPermissionTo($perm);
    $status = $has ? '✓ PUNYA' : '✗ TIDAK';
    echo "  {$status} - {$perm}\n";
}

// Check actual permissions in database
echo "\n\nPermission yang tersimpan di database:\n";
$userPerms = $user->permissions()->where('name', 'like', 'master-mobil%')->get();
foreach ($userPerms as $perm) {
    echo "  - {$perm->name}\n";
}

echo "\n\nKesimpulan:\n";
if ($user->hasPermissionTo('master-mobil-view')) {
    echo "✓ User suci BISA mengakses halaman Data Mobil (master.mobil.index)\n";
    echo "  Route: /master/mobil\n";
    echo "  Permission required: master-mobil-view\n";
} else {
    echo "✗ User suci TIDAK BISA mengakses halaman Data Mobil\n";
}
