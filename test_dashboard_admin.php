<?php

use Illuminate\Support\Facades\Artisan;

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing dashboard permission for admin user:\n";

// Get admin user
$admin = App\Models\User::where('username', 'admin')->first();

if ($admin) {
    echo "Admin user found: {$admin->username}\n";
    
    // Get dashboard permissions
    $dashboardPerms = $admin->permissions()->where('name', 'like', '%dashboard%')->get();
    
    echo "Dashboard permissions for admin:\n";
    foreach ($dashboardPerms as $perm) {
        echo "- {$perm->name}: {$perm->description}\n";
    }
    
    // Test matrix conversion
    $controller = new App\Http\Controllers\UserController();
    $allPermissions = $admin->permissions->pluck('name')->toArray();
    $matrixPerms = $controller->testConvertPermissionsToMatrix($allPermissions);
    
    echo "\nDashboard matrix permissions:\n";
    if (isset($matrixPerms['dashboard'])) {
        foreach ($matrixPerms['dashboard'] as $action => $value) {
            echo "- dashboard[{$action}]: " . ($value ? 'true' : 'false') . "\n";
        }
    } else {
        echo "No dashboard matrix permissions found\n";
    }
    
} else {
    echo "Admin user not found\n";
}