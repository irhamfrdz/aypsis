<?php

require 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Permission;

// Add missing permissions if they don't exist
$permissions = [
    'master-pranota',
    'master-pranota-tagihan-kontainer',
    'master-pembayaran-pranota-supir'
];

foreach ($permissions as $permission) {
    $existing = Permission::where('name', $permission)->first();
    if (!$existing) {
        Permission::create(['name' => $permission]);
        echo "Created permission: $permission\n";
    } else {
        echo "Permission already exists: $permission\n";
    }
}

echo "Permissions check completed!\n";
