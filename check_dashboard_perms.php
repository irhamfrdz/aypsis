<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Permission;

echo "Dashboard permissions in database:\n";
$dashboardPerms = Permission::where('name', 'like', '%dashboard%')->get();

foreach ($dashboardPerms as $perm) {
    echo "- {$perm->name}: {$perm->description}\n";
}

echo "\nTotal dashboard permissions: " . $dashboardPerms->count() . "\n";