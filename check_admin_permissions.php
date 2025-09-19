<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;

$user = User::with('permissions')->where('username', 'admin')->first();
if (!$user) {
    echo 'Admin user not found!' . PHP_EOL;
    exit;
}

echo '=== ADMIN USER PERMISSIONS ===' . PHP_EOL;
echo 'Total permissions: ' . $user->permissions->count() . PHP_EOL;
echo PHP_EOL;

echo 'All permissions:' . PHP_EOL;
foreach($user->permissions as $perm) {
    echo '- ' . $perm->name . PHP_EOL;
}

echo PHP_EOL;
echo '=== CHECKING SPECIFIC PERMISSION ===' . PHP_EOL;
$hasPermission = $user->permissions->contains('name', 'master-kode-nomor-view');
echo 'Has master-kode-nomor-view: ' . ($hasPermission ? 'YES' : 'NO') . PHP_EOL;

echo PHP_EOL;
echo '=== CHECKING KODE NOMOR PERMISSIONS ===' . PHP_EOL;
$allKodeNomorPerms = Permission::where('name', 'like', '%kode-nomor%')->get();
echo 'Total kode-nomor permissions in DB: ' . $allKodeNomorPerms->count() . PHP_EOL;
foreach($allKodeNomorPerms as $perm) {
    echo '- ' . $perm->name . PHP_EOL;
}
