<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CEK PERMISSION PERSETUJUAN USER ===" . PHP_EOL;

// Cek permission yang ada untuk user approval
$userApprovalPermissions = App\Models\Permission::where('name', 'LIKE', '%user%')
    ->where(function($query) {
        $query->where('name', 'LIKE', '%approval%')
              ->orWhere('name', 'LIKE', '%approve%')
              ->orWhere('name', 'LIKE', '%persetujuan%');
    })
    ->get();

echo "Permission persetujuan user yang sudah ada:" . PHP_EOL;
foreach ($userApprovalPermissions as $perm) {
    echo "  - {$perm->name} (ID: {$perm->id}) - {$perm->description}" . PHP_EOL;
}

// Cek juga permission user-approval yang umum
$generalUserPermissions = App\Models\Permission::where('name', 'LIKE', '%user-approval%')
    ->orWhere('name', 'user-approval')
    ->get();

echo PHP_EOL . "Permission user-approval umum:" . PHP_EOL;
foreach ($generalUserPermissions as $perm) {
    echo "  - {$perm->name} (ID: {$perm->id}) - {$perm->description}" . PHP_EOL;
}

// Lihat juga permission untuk user management
$userManagementPermissions = App\Models\Permission::where('name', 'LIKE', 'master-user%')
    ->get();

echo PHP_EOL . "Permission master user yang ada:" . PHP_EOL;
foreach ($userManagementPermissions as $perm) {
    echo "  - {$perm->name} (ID: {$perm->id}) - {$perm->description}" . PHP_EOL;
}

echo PHP_EOL . "=== SELESAI ===" . PHP_EOL;