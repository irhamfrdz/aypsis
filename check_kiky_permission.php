<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::with('permissions')->where('username', 'kiky')->first();

if (!$user) {
    echo "User kiky tidak ditemukan\n";
    exit;
}

echo "User: {$user->name} (ID: {$user->id})\n";
echo "Username: {$user->username}\n";
echo "\n=== All Stock Kontainer Permissions ===\n";

$allPermissions = $user->permissions;
$found = false;
foreach ($allPermissions as $permission) {
    if (str_contains($permission->name, 'stock-kontainer')) {
        echo "✓ {$permission->name}\n";
        $found = true;
    }
}

if (!$found) {
    echo "✗ Tidak ada permission stock-kontainer\n";
}

echo "\n=== Checking Specific Permissions ===\n";
$hasEditPermission = $user->hasPermissionTo('master-stock-kontainer-edit');
echo "master-stock-kontainer-edit: " . ($hasEditPermission ? '✓ ADA' : '✗ TIDAK ADA') . "\n";

$hasViewPermission = $user->hasPermissionTo('master-stock-kontainer-view');
echo "master-stock-kontainer-view: " . ($hasViewPermission ? '✓ ADA' : '✗ TIDAK ADA') . "\n";

$hasCreatePermission = $user->hasPermissionTo('master-stock-kontainer-create');
echo "master-stock-kontainer-create: " . ($hasCreatePermission ? '✓ ADA' : '✗ TIDAK ADA') . "\n";

$hasDeletePermission = $user->hasPermissionTo('master-stock-kontainer-delete');
echo "master-stock-kontainer-delete: " . ($hasDeletePermission ? '✓ ADA' : '✗ TIDAK ADA') . "\n";
