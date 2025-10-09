<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Cek struktur tabel permissions dulu
    $columns = DB::select("DESCRIBE permissions");
    echo "Permissions table structure:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    echo "\n";

    // Permissions yang akan ditambahkan
    $permissions = [
        'pembayaran-dp-ob-view',
        'pembayaran-dp-ob-create',
        'pembayaran-dp-ob-edit',
        'pembayaran-dp-ob-delete',
        'pembayaran-ob-view',
        'pembayaran-ob-create',
        'pembayaran-ob-edit',
        'pembayaran-ob-delete'
    ];

    echo "Starting to add Pembayaran OB permissions...\n\n";

    // Insert permissions ke tabel permissions
    foreach ($permissions as $permissionName) {
        $existingPermission = DB::table('permissions')
            ->where('name', $permissionName)
            ->first();

        if (!$existingPermission) {
            // Insert hanya dengan kolom yang pasti ada
            $insertData = [
                'name' => $permissionName,
                'created_at' => now(),
                'updated_at' => now()
            ];

            DB::table('permissions')->insert($insertData);
            echo "✅ Added permission: {$permissionName}\n";
        } else {
            echo "ℹ️  Permission already exists: {$permissionName}\n";
        }
    }

    echo "\n";

    // Get admin users
    $adminUsers = DB::table('users')
        ->join('user_roles', 'users.id', '=', 'user_roles.user_id')
        ->join('roles', 'user_roles.role_id', '=', 'roles.id')
        ->where('roles.name', 'admin')
        ->select('users.id', 'users.name', 'users.email')
        ->get();

    if ($adminUsers->isEmpty()) {
        // Coba alternatif lain jika tidak ada hasil
        $adminUsers = DB::table('users')
            ->where('email', 'like', '%admin%')
            ->orWhere('name', 'like', '%admin%')
            ->select('id', 'name', 'email')
            ->get();
    }

    if ($adminUsers->isEmpty()) {
        echo "❌ No admin users found!\n";
        echo "Available users:\n";
        $allUsers = DB::table('users')->select('id', 'name', 'email')->get();
        foreach ($allUsers as $user) {
            echo "  - {$user->name} ({$user->email})\n";
        }
        exit(1);
    }

    echo "Found admin users:\n";
    foreach ($adminUsers as $user) {
        echo "  - {$user->name} ({$user->email})\n";
    }
    echo "\n";

    // Assign permissions to admin users
    foreach ($adminUsers as $user) {
        echo "Assigning permissions to user: {$user->name}\n";

        foreach ($permissions as $permissionName) {
            $permissionRecord = DB::table('permissions')
                ->where('name', $permissionName)
                ->first();

            if ($permissionRecord) {
                $existingUserPermission = DB::table('user_permissions')
                    ->where('user_id', $user->id)
                    ->where('permission_id', $permissionRecord->id)
                    ->first();

                if (!$existingUserPermission) {
                    DB::table('user_permissions')->insert([
                        'user_id' => $user->id,
                        'permission_id' => $permissionRecord->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    echo "  ✅ Assigned: {$permissionName}\n";
                } else {
                    echo "  ℹ️  Already assigned: {$permissionName}\n";
                }
            }
        }
        echo "\n";
    }

    echo "🎉 Successfully added Pembayaran OB permissions and assigned to admin users!\n\n";

    // Summary
    echo "Summary of added permissions:\n";
    foreach ($permissions as $permissionName) {
        echo "- {$permissionName}\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
