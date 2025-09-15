<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Checking Perbaikan Kontainer permissions...\n";

$permissions = DB::table('permissions')
    ->where('name', 'like', 'perbaikan-kontainer%')
    ->get(['name', 'description']);

if ($permissions->count() > 0) {
    echo "Found " . $permissions->count() . " perbaikan-kontainer permissions:\n";
    foreach ($permissions as $permission) {
        echo "- {$permission->name}: {$permission->description}\n";
    }
} else {
    echo "No perbaikan-kontainer permissions found!\n";
}

echo "\nChecking for old master-perbaikan-kontainer permissions...\n";
$oldPermissions = DB::table('permissions')
    ->where('name', 'like', 'master-perbaikan-kontainer%')
    ->get(['name', 'description']);

if ($oldPermissions->count() > 0) {
    echo "Found " . $oldPermissions->count() . " old permissions that need to be cleaned up:\n";
    foreach ($oldPermissions as $permission) {
        echo "- {$permission->name}: {$permission->description}\n";
    }
} else {
    echo "No old master-perbaikan-kontainer permissions found. Migration successful!\n";
}
