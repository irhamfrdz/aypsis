<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== PERMISSION BACKEND CHECK ===\n\n";

// Check total permissions
$totalPermissions = App\Models\Permission::count();
echo "Total permissions in database: $totalPermissions\n\n";

// Check dashboard permissions
echo "Dashboard permissions:\n";
$dashboardPerms = App\Models\Permission::where('name', 'like', 'dashboard-%')->get();
foreach($dashboardPerms as $perm) {
    echo "  - {$perm->name}\n";
}
echo "\n";

// Check master permissions
echo "Master permissions:\n";
$masterPerms = App\Models\Permission::where('name', 'like', 'master-%')->take(10)->get();
foreach($masterPerms as $perm) {
    echo "  - {$perm->name}\n";
}
echo "\n";

// Check if matrix permissions exist
echo "Matrix-style permissions check:\n";
$matrixPerms = [
    'dashboard-view',
    'master-user-create',
    'tagihan-kontainer-update',
    'user-approval-approve'
];

foreach($matrixPerms as $permName) {
    $exists = App\Models\Permission::where('name', $permName)->exists();
    echo "  - $permName: " . ($exists ? 'EXISTS' : 'MISSING') . "\n";
}
echo "\n";

// Check user permissions relationship
echo "User permissions relationship check:\n";
$user = App\Models\User::first();
if ($user) {
    echo "First user: {$user->username}\n";
    echo "User permissions count: " . $user->permissions()->count() . "\n";

    $userPerms = $user->permissions()->take(5)->get();
    foreach($userPerms as $perm) {
        echo "  - {$perm->name}\n";
    }
} else {
    echo "No users found\n";
}

echo "\n=== BACKEND CHECK COMPLETE ===";
