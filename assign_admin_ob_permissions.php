<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "=== ASSIGN PEMBAYARAN OB PERMISSIONS TO ADMIN ===\n\n";

    // Permissions yang sudah ditambahkan
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

    // Cari admin users berdasarkan role
    $adminUsers = DB::table('users')
        ->where('role', 'admin')
        ->orWhere('role', 'like', '%admin%')
        ->select('id', 'username', 'role')
        ->get();

    if ($adminUsers->isEmpty()) {
        echo "Tidak ada user dengan role admin. Berikut daftar user yang ada:\n";
        $allUsers = DB::table('users')->select('id', 'username', 'role')->get();
        foreach ($allUsers as $user) {
            echo "  - ID: {$user->id}, Username: {$user->username}, Role: {$user->role}\n";
        }
        echo "\nSilakan jalankan ulang dan pilih user mana yang ingin diberi permission.\n";
        exit(1);
    }

    echo "Found admin users:\n";
    foreach ($adminUsers as $user) {
        echo "  - ID: {$user->id}, Username: {$user->username}, Role: {$user->role}\n";
    }
    echo "\n";

    // Assign permissions to admin users
    foreach ($adminUsers as $user) {
        echo "Assigning permissions to user: {$user->username}\n";

        foreach ($permissions as $permissionName) {
            // Get permission ID
            $permissionRecord = DB::table('permissions')
                ->where('name', $permissionName)
                ->first();

            if ($permissionRecord) {
                // Check if permission already assigned
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
            } else {
                echo "  ❌ Permission not found: {$permissionName}\n";
            }
        }
        echo "\n";
    }

    echo "🎉 SUCCESS! Pembayaran OB permissions have been assigned to admin users!\n\n";

    // Summary
    echo "Summary of assigned permissions:\n";
    foreach ($permissions as $permissionName) {
        echo "- {$permissionName}\n";
    }

    echo "\nAdmin users can now access:\n";
    echo "- Menu: Pembayaran > Aktivitas Lain-lain > Pembayaran DP OB\n";
    echo "- Menu: Pembayaran > Aktivitas Lain-lain > Pembayaran OB\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
