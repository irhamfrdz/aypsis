<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DEBUGGING DASHBOARD PERMISSIONS ===" . PHP_EOL;

// Check if dashboard permissions exist
$dashboardPerm = App\Models\Permission::where('name', 'dashboard')->first();
$dashboardViewPerm = App\Models\Permission::where('name', 'dashboard-view')->first();

echo "Permission 'dashboard': " . ($dashboardPerm ? "✅ EXISTS (ID: {$dashboardPerm->id})" : "❌ NOT FOUND") . PHP_EOL;
echo "Permission 'dashboard-view': " . ($dashboardViewPerm ? "✅ EXISTS (ID: {$dashboardViewPerm->id})" : "❌ NOT FOUND") . PHP_EOL;

// Check what dashboard-related permissions exist
echo PHP_EOL . "All dashboard-related permissions:" . PHP_EOL;
$dashboardPermissions = App\Models\Permission::where('name', 'LIKE', '%dashboard%')->get();
foreach ($dashboardPermissions as $perm) {
    echo "  - {$perm->name} (ID: {$perm->id})" . PHP_EOL;
}

// Check admin user's dashboard permissions
echo PHP_EOL . "Admin user's dashboard permissions:" . PHP_EOL;
$admin = App\Models\User::where('username', 'admin')->first();
if ($admin) {
    $adminDashboardPerms = $admin->permissions()->where('name', 'LIKE', '%dashboard%')->get();
    foreach ($adminDashboardPerms as $perm) {
        echo "  - {$perm->name} (ID: {$perm->id})" . PHP_EOL;
    }
} else {
    echo "  Admin user not found!" . PHP_EOL;
}

echo PHP_EOL . "=== SELESAI ===" . PHP_EOL;