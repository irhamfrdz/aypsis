<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Adding Gate In, Master Terminal, and Master Service permissions to admin user...\n\n";

    // All permissions yang akan diberikan ke admin
    $permissions = [
        // Gate In permissions
        'gate-in-view' => 'Melihat data Gate In',
        'gate-in-create' => 'Membuat Gate In baru',
        'gate-in-update' => 'Mengubah data Gate In',
        'gate-in-delete' => 'Menghapus data Gate In',

        // Master Terminal permissions
        'master-terminal-view' => 'Melihat data Master Terminal',
        'master-terminal-create' => 'Membuat Master Terminal baru',
        'master-terminal-update' => 'Mengubah data Master Terminal',
        'master-terminal-delete' => 'Menghapus data Master Terminal',

        // Master Service permissions
        'master-service-view' => 'Melihat data Master Service',
        'master-service-create' => 'Membuat Master Service baru',
        'master-service-update' => 'Mengubah data Master Service',
        'master-service-delete' => 'Menghapus data Master Service',
    ];

    echo "1. Creating permissions if they don't exist...\n";

    foreach ($permissions as $name => $description) {
        $permission = DB::table('permissions')->where('name', $name)->first();

        if (!$permission) {
            DB::table('permissions')->insert([
                'name' => $name,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "   ✓ Created permission: {$name}\n";
        } else {
            echo "   - Permission already exists: {$name}\n";
        }
    }

    echo "\n2. Finding admin user...\n";

    // Cari user admin
    $adminUser = DB::table('users')->where('username', 'admin')->first();

    if (!$adminUser) {
        // Coba cari user dengan email admin atau role admin
        $adminUser = DB::table('users')->where('email', 'admin@admin.com')->first();

        if (!$adminUser) {
            // Cari user pertama (biasanya admin)
            $adminUser = DB::table('users')->orderBy('id')->first();
        }
    }

    if (!$adminUser) {
        echo "   ⚠ No admin user found! Please create an admin user first.\n";
        exit(1);
    }

    echo "   ✓ Found user: {$adminUser->username} (ID: {$adminUser->id})\n";

    echo "\n3. Assigning permissions to admin user...\n";

    foreach (array_keys($permissions) as $permissionName) {
        $permission = DB::table('permissions')->where('name', $permissionName)->first();

        if ($permission) {
            // Check if user already has this permission
            $existing = DB::table('user_permissions')
                ->where('user_id', $adminUser->id)
                ->where('permission_id', $permission->id)
                ->first();

            if (!$existing) {
                DB::table('user_permissions')->insert([
                    'user_id' => $adminUser->id,
                    'permission_id' => $permission->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                echo "   ✓ Assigned {$permissionName} to admin\n";
            } else {
                echo "   - Admin already has permission: {$permissionName}\n";
            }
        }
    }

    echo "\n" . str_repeat("=", 60) . "\n";
    echo "✓ PERMISSIONS SETUP COMPLETED SUCCESSFULLY!\n";
    echo str_repeat("=", 60) . "\n";

    echo "\nAdmin user '{$adminUser->username}' now has the following permissions:\n\n";

    echo "📋 GATE IN PERMISSIONS:\n";
    echo "   - gate-in-view (Melihat data Gate In)\n";
    echo "   - gate-in-create (Membuat Gate In baru)\n";
    echo "   - gate-in-update (Mengubah data Gate In)\n";
    echo "   - gate-in-delete (Menghapus data Gate In)\n\n";

    echo "🏢 MASTER TERMINAL PERMISSIONS:\n";
    echo "   - master-terminal-view (Melihat data Master Terminal)\n";
    echo "   - master-terminal-create (Membuat Master Terminal baru)\n";
    echo "   - master-terminal-update (Mengubah data Master Terminal)\n";
    echo "   - master-terminal-delete (Menghapus data Master Terminal)\n\n";

    echo "⚙️ MASTER SERVICE PERMISSIONS:\n";
    echo "   - master-service-view (Melihat data Master Service)\n";
    echo "   - master-service-create (Membuat Master Service baru)\n";
    echo "   - master-service-update (Mengubah data Master Service)\n";
    echo "   - master-service-delete (Menghapus data Master Service)\n\n";

    echo "🎯 Next steps:\n";
    echo "   1. Login as admin user to test the permissions\n";
    echo "   2. Access Gate In menu at /gate-in\n";
    echo "   3. Create Master Terminals and Services as needed\n";
    echo "   4. Test the complete Gate In workflow\n\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
