<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $permissions = [
        'pergerakan-kontainer-view',
        'pergerakan-kontainer-create',
        'pergerakan-kontainer-update',
        'pergerakan-kontainer-delete',
        'pergerakan-kontainer-approve',
        'pergerakan-kontainer-print',
        'pergerakan-kontainer-export',
    ];

    $now = now();
    $addedPermissions = [];

    foreach ($permissions as $permission) {
        // Check if permission already exists
        $exists = DB::table('permissions')->where('name', $permission)->exists();
        
        if (!$exists) {
            // Insert permission
            $id = DB::table('permissions')->insertGetId([
                'name' => $permission,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            
            $addedPermissions[] = [
                'id' => $id,
                'name' => $permission
            ];
            
            echo "✓ Permission '{$permission}' berhasil ditambahkan dengan ID: {$id}\n";
        } else {
            echo "⊘ Permission '{$permission}' sudah ada dalam database\n";
        }
    }

    if (!empty($addedPermissions)) {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "Menambahkan permission ke admin users...\n";
        echo str_repeat("=", 60) . "\n\n";

        // Get all admin users by checking users table directly
        $adminUsers = DB::table('users')
            ->where('level', 'admin')
            ->orWhere('id', 1) // Fallback to user ID 1 as admin
            ->select('id', 'name', 'email')
            ->get();

        if ($adminUsers->isEmpty()) {
            echo "⚠ Tidak ada admin users ditemukan\n";
        } else {
            foreach ($adminUsers as $user) {
                foreach ($addedPermissions as $permission) {
                    // Check if user already has this permission
                    $hasPermission = DB::table('model_has_permissions')
                        ->where('permission_id', $permission['id'])
                        ->where('model_id', $user->id)
                        ->where('model_type', 'App\Models\User')
                        ->exists();

                    if (!$hasPermission) {
                        DB::table('model_has_permissions')->insert([
                            'permission_id' => $permission['id'],
                            'model_type' => 'App\Models\User',
                            'model_id' => $user->id,
                        ]);
                        
                        echo "✓ Permission '{$permission['name']}' ditambahkan ke user: {$user->name} ({$user->email})\n";
                    }
                }
            }
        }

        echo "\n" . str_repeat("=", 60) . "\n";
        echo "RINGKASAN\n";
        echo str_repeat("=", 60) . "\n";
        echo "Total permission baru ditambahkan: " . count($addedPermissions) . "\n";
        echo "Total admin users diupdate: " . $adminUsers->count() . "\n";
        echo str_repeat("=", 60) . "\n";
    } else {
        echo "\n⚠ Tidak ada permission baru yang ditambahkan\n";
    }

    echo "\n✓ Script selesai dijalankan!\n";

} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
