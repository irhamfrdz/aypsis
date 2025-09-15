<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "=== CHECKING PERMISSIONS IN DATABASE ===\n";
$permissions = Permission::all();
echo "Total permissions: " . $permissions->count() . "\n\n";

foreach($permissions as $perm) {
    echo "ID: {$perm->id}, Name: {$perm->name}, Description: " . ($perm->description ?? 'N/A') . "\n";
}

echo "\n=== CHECKING SPECIFIC PERMISSIONS ===\n";
$dashboardPerm = Permission::where('name', 'dashboard')->first();
echo "Dashboard permission exists: " . ($dashboardPerm ? 'YES (ID: ' . $dashboardPerm->id . ')' : 'NO') . "\n";

$masterUserPerm = Permission::where('name', 'master-user')->first();
echo "Master-user permission exists: " . ($masterUserPerm ? 'YES (ID: ' . $masterUserPerm->id . ')' : 'NO') . "\n";
