<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

try {
    // Create all approval surat jalan permissions
    $permissions = [
        'approval-surat-jalan-view',
        'approval-surat-jalan-create',
        'approval-surat-jalan-update',
        'approval-surat-jalan-delete',
        'approval-surat-jalan-approve',
        'approval-surat-jalan-reject'
    ];

    echo "Creating permissions...\n";
    echo "─────────────────────────────────────────\n";

    foreach ($permissions as $permissionName) {
        $permission = DB::table('permissions')->where('name', $permissionName)->first();
        
        if (!$permission) {
            DB::table('permissions')->insert([
                'name' => $permissionName,
                'description' => 'Permission for ' . $permissionName,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "✓ Permission '$permissionName' created\n";
        } else {
            echo "• Permission '$permissionName' already exists\n";
        }
    }

    // Get all admin users
    $adminUsers = User::whereHas('roles', function($query) {
        $query->where('name', 'admin');
    })->get();

    if ($adminUsers->isEmpty()) {
        echo "✗ No admin users found\n";
        exit(1);
    }

    echo "\nGranting permissions to admin users...\n";
    echo "─────────────────────────────────────────\n";

    foreach ($adminUsers as $user) {
        $grantedCount = 0;
        $existingCount = 0;

        foreach ($permissions as $permissionName) {
            $permission = DB::table('permissions')->where('name', $permissionName)->first();
            
            if ($permission) {
                $hasPermission = DB::table('user_permissions')
                    ->where('user_id', $user->id)
                    ->where('permission_id', $permission->id)
                    ->exists();
                    
                if (!$hasPermission) {
                    DB::table('user_permissions')->insert([
                        'user_id' => $user->id,
                        'permission_id' => $permission->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $grantedCount++;
                } else {
                    $existingCount++;
                }
            }
        }

        if ($grantedCount > 0) {
            echo "✓ {$user->name} ({$user->email}) - {$grantedCount} new permissions granted";
            if ($existingCount > 0) {
                echo ", {$existingCount} already existed";
            }
            echo "\n";
        } else {
            echo "• {$user->name} ({$user->email}) - All permissions already exist\n";
        }
    }

    echo "─────────────────────────────────────────\n";
    echo "✓ Successfully completed! All approval surat jalan permissions set up.\n";

} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
