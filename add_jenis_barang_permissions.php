<?php
// Script untuk menambahkan permissions Master Jenis Barang

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Models\User;

try {
    echo "🔧 Adding Master Jenis Barang Permissions...\n\n";

    // Define permissions for master jenis barang
    $permissions = [
        'master-jenis-barang-view' => 'View Master Jenis Barang',
        'master-jenis-barang-create' => 'Create Master Jenis Barang',
        'master-jenis-barang-update' => 'Update Master Jenis Barang',
        'master-jenis-barang-delete' => 'Delete Master Jenis Barang'
    ];

    $createdPermissions = [];

    // Create permissions if they don't exist
    foreach ($permissions as $permissionName => $description) {
        $permission = Permission::firstOrCreate(
            ['name' => $permissionName],
            ['description' => $description]
        );

        if ($permission->wasRecentlyCreated) {
            $createdPermissions[] = $permissionName;
            echo "✅ Created permission: {$permissionName}\n";
        } else {
            echo "⏭️  Permission already exists: {$permissionName}\n";
        }
    }

    // Get user admin
    $userAdmin = User::where('username', 'admin')->first();

    if ($userAdmin) {
        foreach ($permissions as $permissionName => $description) {
            $permission = Permission::where('name', $permissionName)->first();

            if ($permission && !$userAdmin->permissions->contains('id', $permission->id)) {
                $userAdmin->permissions()->attach($permission->id);
                echo "✅ Added permission {$permissionName} to user admin\n";
            } else {
                echo "⏭️  User admin already has permission: {$permissionName}\n";
            }
        }
    } else {
        echo "⚠️  User admin not found\n";
    }

    echo "\n🎉 Master Jenis Barang permissions setup completed!\n";
    echo "📋 Summary:\n";
    echo "   - Created " . count($createdPermissions) . " new permissions\n";
    echo "   - All permissions assigned to admin user\n";

    if (!empty($createdPermissions)) {
        echo "\n📝 New permissions created:\n";
        foreach ($createdPermissions as $permission) {
            echo "   • {$permission}\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
