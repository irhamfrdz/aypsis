<?php

use Illuminate\Support\Facades\DB;

try {
    // Define permissions for pergerakan kapal module
    $permissions = [
        'pergerakan-kapal.view',
        'pergerakan-kapal.create',
        'pergerakan-kapal.edit',
        'pergerakan-kapal.delete',
    ];

    // Insert permissions
    foreach ($permissions as $permission) {
        DB::table('permissions')->insertOrIgnore([
            'name' => $permission,
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✓ Permission '{$permission}' added successfully\n";
    }

    // Get admin user
    $adminUser = DB::table('users')->where('email', 'admin@aypsis.com')->first();

    if ($adminUser) {
        // Get permission IDs
        $permissionIds = DB::table('permissions')
            ->whereIn('name', $permissions)
            ->pluck('id');

        // Assign permissions to admin user
        foreach ($permissionIds as $permissionId) {
            DB::table('model_has_permissions')->insertOrIgnore([
                'permission_id' => $permissionId,
                'model_type' => 'App\\Models\\User',
                'model_id' => $adminUser->id,
            ]);
        }

        echo "✓ All pergerakan kapal permissions assigned to admin user\n";
    } else {
        echo "⚠ Admin user not found. Please assign permissions manually\n";
    }

    echo "\n✅ Pergerakan Kapal permissions setup completed successfully!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
