<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

echo "=== TESTING ADMIN USER LOGIN SIMULATION ===\n\n";

// Find admin user
$adminUser = DB::table('users')->where('username', 'admin')->first();

if (!$adminUser) {
    echo "✗ Admin user not found! Creating one...\n";

    // Create admin user if not exists
    $adminId = DB::table('users')->insertGetId([
        'username' => 'admin',
        'password' => bcrypt('password'),
        'karyawan_id' => null,
        'created_at' => now(),
        'updated_at' => now()
    ]);

    $adminUser = DB::table('users')->find($adminId);
    echo "✓ Admin user created with ID: $adminId\n";
}

if ($adminUser) {
    echo "✓ Admin user found: {$adminUser->username} (ID: {$adminUser->id})\n";

    // Check if admin has kode nomor permissions
    $kodeNomorPermissions = [
        'master-kode-nomor-view',
        'master-kode-nomor-create',
        'master-kode-nomor-update',
        'master-kode-nomor-delete'
    ];

    $assignedPermissions = DB::table('user_permissions')
        ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
        ->where('user_permissions.user_id', $adminUser->id)
        ->whereIn('permissions.name', $kodeNomorPermissions)
        ->pluck('permissions.name')
        ->toArray();

    echo "\nAdmin user permissions for kode nomor:\n";
    foreach ($kodeNomorPermissions as $perm) {
        $hasPermission = in_array($perm, $assignedPermissions);
        $status = $hasPermission ? '✓' : '✗';
        echo "$status $perm\n";
    }

    // If admin doesn't have permissions, assign them
    $missingPermissions = array_diff($kodeNomorPermissions, $assignedPermissions);

    if (count($missingPermissions) > 0) {
        echo "\nAssigning missing permissions to admin user...\n";

        foreach ($missingPermissions as $permName) {
            $permission = DB::table('permissions')->where('name', $permName)->first();

            if ($permission) {
                DB::table('user_permissions')->insert([
                    'user_id' => $adminUser->id,
                    'permission_id' => $permission->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                echo "✓ Assigned: $permName\n";
            } else {
                echo "✗ Permission not found: $permName\n";
            }
        }
    } else {
        echo "\n✓ Admin user already has all kode nomor permissions!\n";
    }

    // Simulate login for testing
    echo "\n=== SIMULATING SIDEBAR RENDERING WITH ADMIN USER ===\n";

    // Check permission via database (simulating what happens in sidebar)
    $canViewKodeNomor = DB::table('user_permissions')
        ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
        ->where('user_permissions.user_id', $adminUser->id)
        ->where('permissions.name', 'master-kode-nomor-view')
        ->exists();

    if ($canViewKodeNomor) {
        echo "✓ Menu 'Kode Nomor' SHOULD BE VISIBLE in sidebar for admin user\n";
        echo "✓ Route URL: http://localhost/master/kode-nomor\n";
    } else {
        echo "✗ Menu 'Kode Nomor' will be HIDDEN (permission denied)\n";
    }

    echo "\n=== FINAL STATUS ===\n";
    echo "Admin user: {$adminUser->username}\n";
    echo "Menu visibility: " . ($canViewKodeNomor ? 'VISIBLE' : 'HIDDEN') . "\n";
    echo "Login credentials: username='admin', password='password'\n";

} else {
    echo "✗ Failed to create/find admin user\n";
}

echo "\n=== NEXT STEPS ===\n";
echo "1. Login to the application with admin credentials\n";
echo "2. Check if 'Kode Nomor' menu appears in the sidebar\n";
echo "3. If still not visible, clear browser cache (Ctrl+F5)\n";
echo "4. If still not visible, check browser developer tools for JavaScript errors\n";
