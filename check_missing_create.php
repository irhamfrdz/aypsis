<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$admin = User::where('username', 'admin')->with('permissions')->first();

echo "=== CHECKING CREATE PERMISSIONS ===\n\n";

if ($admin) {
    echo "Cabang CREATE related permissions:\n";
    $cabangCreate = $admin->permissions->filter(function($perm) {
        return str_contains($perm->name, 'cabang') && str_contains($perm->name, 'create');
    });
    foreach ($cabangCreate as $perm) {
        echo "- {$perm->name}\n";
    }

    echo "\nCOA CREATE related permissions:\n";
    $coaCreate = $admin->permissions->filter(function($perm) {
        return str_contains($perm->name, 'coa') && str_contains($perm->name, 'create');
    });
    foreach ($coaCreate as $perm) {
        echo "- {$perm->name}\n";
    }

    // Check if admin has any permissions that might be missing
    echo "\nAll permissions missing from admin:\n";
    $allPermissions = \App\Models\Permission::all();
    $adminPermissionNames = $admin->permissions->pluck('name')->toArray();

    $missingCabang = $allPermissions->filter(function($perm) use ($adminPermissionNames) {
        return str_contains($perm->name, 'cabang') && str_contains($perm->name, 'create') && !in_array($perm->name, $adminPermissionNames);
    });

    $missingCoa = $allPermissions->filter(function($perm) use ($adminPermissionNames) {
        return str_contains($perm->name, 'coa') && str_contains($perm->name, 'create') && !in_array($perm->name, $adminPermissionNames);
    });

    echo "Missing CABANG CREATE permissions:\n";
    foreach ($missingCabang as $perm) {
        echo "- {$perm->name}\n";
    }

    echo "\nMissing COA CREATE permissions:\n";
    foreach ($missingCoa as $perm) {
        echo "- {$perm->name}\n";
    }
}
