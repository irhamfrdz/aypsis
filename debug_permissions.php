<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$userId = 30; // user_admin ID

echo "Checking permissions for user ID: {$userId}\n";

// Check user_permissions table
$userPermissions = DB::table('user_permissions')
    ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
    ->where('user_permissions.user_id', $userId)
    ->where('permissions.name', 'like', 'master-nomor-terakhir%')
    ->select('permissions.name')
    ->get();

echo "Direct database check:\n";
if ($userPermissions->count() > 0) {
    foreach ($userPermissions as $perm) {
        echo "✅ {$perm->name}\n";
    }
} else {
    echo "❌ No permissions found in database\n";
}

// Check permissions table
$allNomorTerakhirPerms = DB::table('permissions')
    ->where('name', 'like', 'master-nomor-terakhir%')
    ->select('id', 'name')
    ->get();

echo "\nAll master-nomor-terakhir permissions in database:\n";
foreach ($allNomorTerakhirPerms as $perm) {
    echo "- {$perm->name} (ID: {$perm->id})\n";
}
