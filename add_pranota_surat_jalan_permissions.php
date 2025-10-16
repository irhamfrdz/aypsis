<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

// Bootstrap Laravel
$app = Application::configure(basePath: dirname(__DIR__, 2))
    ->withRouting(
        web: __DIR__ . '/../../routes/web.php',
        commands: __DIR__ . '/../../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// Run the application bootstrap
$app->booted(function () {
    echo "🔧 Adding Pranota Surat Jalan Permissions...\n\n";

    // Define permissions
    $permissions = [
        'pranota-surat-jalan-view',
        'pranota-surat-jalan-create',
        'pranota-surat-jalan-update',
        'pranota-surat-jalan-delete',
    ];

    // Create permissions
    foreach ($permissions as $permissionName) {
        $permission = Permission::firstOrCreate(['name' => $permissionName]);
        echo "✅ Permission created/found: {$permissionName}\n";
    }

    echo "\n";

    // Get admin role and assign permissions
    $adminRole = Role::where('name', 'admin')->first();
    if ($adminRole) {
        foreach ($permissions as $permissionName) {
            if (!$adminRole->hasPermissionTo($permissionName)) {
                $adminRole->givePermissionTo($permissionName);
                echo "✅ Assigned '{$permissionName}' to admin role\n";
            } else {
                echo "⚠️  Permission '{$permissionName}' already assigned to admin role\n";
            }
        }
    } else {
        echo "❌ Admin role not found!\n";
    }

    echo "\n";

    // Get specific user (assuming user ID 1 is admin)
    $adminUser = User::find(1);
    if ($adminUser) {
        foreach ($permissions as $permissionName) {
            if (!$adminUser->hasPermissionTo($permissionName)) {
                $adminUser->givePermissionTo($permissionName);
                echo "✅ Assigned '{$permissionName}' to user: {$adminUser->name}\n";
            } else {
                echo "⚠️  Permission '{$permissionName}' already assigned to user: {$adminUser->name}\n";
            }
        }
    } else {
        echo "❌ Admin user (ID: 1) not found!\n";
    }

    echo "\n🎉 Pranota Surat Jalan permissions setup completed!\n";
    echo "📝 Available permissions:\n";
    foreach ($permissions as $permission) {
        echo "   - {$permission}\n";
    }
    echo "\n";
});

// Boot the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
