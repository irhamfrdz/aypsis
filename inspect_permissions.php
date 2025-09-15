<?php

require_once 'vendor/autoload.php';

use App\Models\Permission;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

function printPermissionsForPrefix($prefix) {
    $perms = Permission::where('name', 'like', $prefix . '%')->get();
    echo "\nPermissions matching: $prefix\n";
    foreach ($perms as $p) {
        echo "ID: {$p->id}  Name: {$p->name}\n";
    }
}

printPermissionsForPrefix('permohonan');
printPermissionsForPrefix('pranota-supir');
printPermissionsForPrefix('pembayaran-pranota-supir');
printPermissionsForPrefix('master.karyawan');
printPermissionsForPrefix('master-karyawan');

echo "\nDone\n";
