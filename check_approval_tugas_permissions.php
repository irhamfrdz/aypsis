<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "Checking approval permissions in database...\n\n";

$approvalPermissions = Permission::where('name', 'like', 'approval-%')->get();
echo "Found " . $approvalPermissions->count() . " approval permissions:\n";
foreach ($approvalPermissions as $perm) {
    echo "- {$perm->name} (ID: {$perm->id})\n";
}

echo "\nChecking approval-permohonan-memo permissions...\n";
$approvalMemoPermissions = Permission::where('name', 'like', 'approval-permohonan-memo-%')->get();
echo "Found " . $approvalMemoPermissions->count() . " approval-permohonan-memo permissions:\n";
foreach ($approvalMemoPermissions as $perm) {
    echo "- {$perm->name} (ID: {$perm->id})\n";
}

echo "\nTest completed.\n";
