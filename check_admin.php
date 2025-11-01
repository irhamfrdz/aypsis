<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$admin = App\Models\User::where('username', 'admin')->first();
if ($admin) {
    $dashboardPerms = $admin->permissions()->where('name', 'like', '%dashboard%')->count();
    $totalPerms = $admin->permissions()->count();
    echo "Admin user has {$dashboardPerms} dashboard permissions out of {$totalPerms} total permissions\n";
    
    $totalSystemPerms = App\Models\Permission::count();
    echo "Total permissions in system: {$totalSystemPerms}\n";
    
    if ($totalPerms == $totalSystemPerms) {
        echo "✅ Admin has ALL permissions including dashboard permissions!\n";
    } else {
        echo "⚠️ Admin missing some permissions\n";
    }
} else {
    echo "Admin user not found\n";
}