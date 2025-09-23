<?php

require_once 'vendor/autoload.php';

use App\Models\Permission;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$perms = Permission::where('name', 'like', 'approval%')->get();

echo "=== APPROVAL PERMISSIONS IN DATABASE ===\n";
foreach ($perms as $p) {
    echo "- {$p->name}\n";
}

echo "\n=== CHECKING SPECIFIC PERMISSIONS ===\n";
$checkPerms = ['approval-dashboard', 'approval.view', 'approval.approve', 'approval-view', 'approval-approve'];
foreach ($checkPerms as $permName) {
    $perm = Permission::where('name', $permName)->first();
    echo "- $permName: " . ($perm ? 'EXISTS' : 'NOT FOUND') . "\n";
}

echo "\nTest completed.\n";
