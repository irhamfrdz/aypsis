<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== Adding Nomor Terakhir Permissions to Admin User ===\n";

$user = User::find(1);
if (!$user) {
    echo "❌ User with ID 1 not found\n";
    exit;
}

echo "User: {$user->username}\n";

$permissions = Permission::where('name', 'like', 'master-nomor-terakhir%')->get();
echo "Found " . $permissions->count() . " master-nomor-terakhir permissions\n";

foreach($permissions as $perm) {
    $user->givePermissionTo($perm);
    echo "✅ Added: {$perm->name}\n";
}

echo "\n=== Verification ===\n";
$userPermissions = $user->permissions->where('name', 'like', 'master-nomor-terakhir%')->pluck('name');
echo "User now has " . $userPermissions->count() . " nomor terakhir permissions:\n";
foreach($userPermissions as $perm) {
    echo "- {$perm}\n";
}

echo "\n✅ Done!\n";
