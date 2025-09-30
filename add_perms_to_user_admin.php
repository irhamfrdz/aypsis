<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

$user = User::where('username', 'user_admin')->first();

if (!$user) {
    echo "❌ User 'user_admin' not found\n";
    exit;
}

echo "Adding permissions to user: {$user->username} (ID: {$user->id})\n";

$permissions = ['master-nomor-terakhir-view', 'master-nomor-terakhir-create', 'master-nomor-terakhir-update', 'master-nomor-terakhir-delete'];

foreach($permissions as $permName) {
    $perm = Permission::where('name', $permName)->first();
    if($perm && !$user->hasPermissionTo($permName)) {
        $user->permissions()->attach($perm);
        echo "✅ Added: {$permName}\n";
    } else {
        echo "ℹ️ Skipped: {$permName} (already has or not found)\n";
    }
}

echo "\nVerifying permissions:\n";
$nomorTerakhirPerms = $user->permissions->where('name', 'like', 'master-nomor-terakhir%')->pluck('name')->toArray();
foreach($nomorTerakhirPerms as $perm) {
    echo "✅ {$perm}\n";
}

echo "\n✅ Done! User user_admin now has master nomor terakhir permissions.\n";
