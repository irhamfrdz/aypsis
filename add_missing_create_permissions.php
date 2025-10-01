<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== ADDING MISSING CREATE PERMISSIONS TO ADMIN ===\n\n";

$admin = User::where('username', 'admin')->first();

if ($admin) {
    $missingPermissions = [
        'master-cabang-create',
        'master-coa-create'
    ];

    foreach ($missingPermissions as $permName) {
        $permission = Permission::where('name', $permName)->first();
        if ($permission) {
            if (!$admin->permissions->contains('id', $permission->id)) {
                $admin->permissions()->attach($permission->id);
                echo "✅ Added permission: {$permName}\n";
            } else {
                echo "⚠️  Permission already exists: {$permName}\n";
            }
        } else {
            echo "❌ Permission not found in database: {$permName}\n";
        }
    }

    echo "\nTesting permissions after adding:\n";
    $admin->refresh();
    $admin->load('permissions');

    echo "- master-cabang-create: " . ($admin->can('master-cabang-create') ? '✅ GRANTED' : '❌ DENIED') . "\n";
    echo "- master-coa-create: " . ($admin->can('master-coa-create') ? '✅ GRANTED' : '❌ DENIED') . "\n";

} else {
    echo "❌ User admin not found!\n";
}
