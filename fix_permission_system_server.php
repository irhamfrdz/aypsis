<?php
require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== FIXING PERMISSION SYSTEM MIGRATION ===\n\n";

try {
    // Check current permission system structure
    echo "1. Current Permission System Tables:\n";

    $tables = ['permissions', 'roles', 'permission_role', 'user_permissions'];
    foreach ($tables as $table) {
        try {
            $count = DB::table($table)->count();
            echo "   ✓ $table - $count records\n";
        } catch (Exception $e) {
            echo "   ✗ $table - ERROR: " . $e->getMessage() . "\n";
        }
    }

    echo "\n2. Checking permission_role table structure:\n";
    try {
        $columns = DB::select("DESCRIBE permission_role");
        foreach ($columns as $col) {
            echo "   - {$col->Field} ({$col->Type})\n";
        }
    } catch (Exception $e) {
        echo "   ERROR: " . $e->getMessage() . "\n";
    }

    echo "\n3. Finding Admin Role:\n";
    $adminRole = DB::table('roles')->where('name', 'Admin')->first();
    if ($adminRole) {
        echo "   ✓ Admin role found - ID: {$adminRole->id}\n";

        // Check permissions for Master Tujuan Kegiatan Utama
        echo "\n4. Checking Tujuan Kegiatan Utama permissions:\n";
        $tujuanPermissions = DB::table('permissions')
            ->where('name', 'like', '%tujuan-kegiatan-utama%')
            ->get();

        foreach ($tujuanPermissions as $perm) {
            echo "   - {$perm->name} (ID: {$perm->id})\n";

            // Check if admin has this permission
            $hasPermission = DB::table('permission_role')
                ->where('role_id', $adminRole->id)
                ->where('permission_id', $perm->id)
                ->exists();

            if ($hasPermission) {
                echo "     ✓ Admin has this permission\n";
            } else {
                echo "     ✗ Admin missing this permission - ADDING...\n";

                DB::table('permission_role')->insert([
                    'role_id' => $adminRole->id,
                    'permission_id' => $perm->id,
                ]);

                echo "     ✓ Permission added to Admin\n";
            }
        }

    } else {
        echo "   ✗ Admin role not found!\n";
    }

    echo "\n5. Summary:\n";
    $adminPermCount = DB::table('permission_role')
        ->where('role_id', $adminRole->id ?? 0)
        ->count();
    echo "   - Admin role has $adminPermCount permissions\n";

    $tujuanPermCount = DB::table('permissions')
        ->where('name', 'like', '%tujuan-kegiatan-utama%')
        ->count();
    echo "   - Total Tujuan Kegiatan Utama permissions: $tujuanPermCount\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== PERMISSION FIX COMPLETED ===\n";
