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

echo "\n=== CHECKING PRANOTA PERBAIKAN KONTAINER PERMISSIONS ===\n";

$pranotaPerms = [
    'pranota-perbaikan-kontainer.view',
    'pranota-perbaikan-kontainer.delete',
    'pranota-perbaikan-kontainer-view',
    'pranota-perbaikan-kontainer-delete'
];

foreach ($pranotaPerms as $permName) {
    $perm = Permission::where('name', $permName)->first();
    echo "- {$permName}: " . ($perm ? 'EXISTS (ID: ' . $perm->id . ')' : 'NOT FOUND') . "\n";
}

echo "\n=== ALL PRANOTA-RELATED PERMISSIONS ===\n";
$allPranotaPerms = Permission::where('name', 'like', '%pranota-perbaikan-kontainer%')->get();
foreach ($allPranotaPerms as $perm) {
    echo "- {$perm->name} (ID: {$perm->id})\n";
}
