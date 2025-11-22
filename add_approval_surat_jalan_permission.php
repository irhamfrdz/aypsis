<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

try {
    // Create permission if doesn't exist
    $permissionName = 'approval-surat-jalan-view';
    
    $permission = DB::table('permissions')->where('name', $permissionName)->first();
    
    if (!$permission) {
        DB::table('permissions')->insert([
            'name' => $permissionName,
            'guard_name' => 'web',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $permission = DB::table('permissions')->where('name', $permissionName)->first();
        echo "✓ Permission '$permissionName' created\n";
    } else {
        echo "✓ Permission '$permissionName' already exists\n";
    }

    // Get all admin users
    $adminUsers = User::whereHas('roles', function($query) {
        $query->where('name', 'admin');
    })->get();

    if ($adminUsers->isEmpty()) {
        echo "✗ No admin users found\n";
        exit(1);
    }

    echo "\nFound " . $adminUsers->count() . " admin user(s)\n";
    echo "─────────────────────────────────────────\n";

    foreach ($adminUsers as $user) {
        // Check if permission exists in user_permissions table
        $hasPermission = DB::table('user_permissions')
            ->where('user_id', $user->id)
            ->where('permission_id', $permission->id)
            ->exists();
            
        if ($hasPermission) {
            echo "• {$user->name} ({$user->email}) - Already has permission ✓\n";
        } else {
            // Give permission to user via user_permissions table
            try {
                DB::table('user_permissions')->insert([
                    'user_id' => $user->id,
                    'permission_id' => $permission->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                echo "• {$user->name} ({$user->email}) - Permission granted ✓\n";
            } catch (\Exception $e) {
                echo "• {$user->name} ({$user->email}) - Error: " . $e->getMessage() . "\n";
            }
        }
    }

    echo "─────────────────────────────────────────\n";
    echo "✓ Successfully granted '$permissionName' permission to all admin users\n";

} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
