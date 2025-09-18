<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Models\User;

echo "=== CHECKING PERBAIKAN KONTAINER PERMISSIONS ===\n";

echo "\n1. Checking perbaikan-kontainer permissions in database:\n";
$perms = Permission::where('name', 'like', '%perbaikan-kontainer%')->get(['id', 'name', 'description']);
foreach($perms as $perm) {
    echo "- {$perm->name} ({$perm->description})\n";
}
echo "Total: " . $perms->count() . " permissions\n";

echo "\n2. Checking user_admin permissions:\n";
$userAdmin = User::where('username', 'user_admin')->first();
if ($userAdmin) {
    $userPerms = $userAdmin->permissions->pluck('name')->toArray();
    $perbaikanPerms = array_filter($userPerms, function($perm) {
        return strpos($perm, 'perbaikan-kontainer') !== false;
    });

    echo "user_admin has " . count($perbaikanPerms) . " perbaikan-kontainer permissions:\n";
    foreach($perbaikanPerms as $perm) {
        echo "- $perm\n";
    }

    // Check specific permission
    $hasCreate = in_array('perbaikan-kontainer-create', $userPerms);
    echo "\nSpecific check - perbaikan-kontainer-create: " . ($hasCreate ? 'YES' : 'NO') . "\n";
} else {
    echo "user_admin not found!\n";
}

echo "\n3. Checking all permissions containing 'perbaikan':\n";
$allPerms = Permission::where('name', 'like', '%perbaikan%')->get(['id', 'name', 'description']);
foreach($allPerms as $perm) {
    echo "- {$perm->name} ({$perm->description})\n";
}
echo "Total: " . $allPerms->count() . " permissions\n";
