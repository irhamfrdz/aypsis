<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "===========================================\n";
    echo "REFRESH PERMISSIONS UNTUK SEMUA USER ADMIN\n";
    echo "===========================================\n\n";

    // Get admin role
    $adminRole = DB::table('roles')->where('name', 'admin')->first();
    
    if (!$adminRole) {
        echo "❌ Role admin tidak ditemukan!\n";
        exit(1);
    }

    // Get OB Bongkar permissions
    $obBongkarPermissions = DB::table('permissions')
        ->where('name', 'LIKE', 'ob-bongkar-%')
        ->pluck('id', 'name');

    echo "Permissions OB Bongkar yang ditemukan:\n";
    foreach ($obBongkarPermissions as $name => $id) {
        echo "  - $name (ID: $id)\n";
    }
    echo "\n";

    // Ensure permissions are assigned to admin role
    foreach ($obBongkarPermissions as $name => $permId) {
        $exists = DB::table('permission_role')
            ->where('role_id', $adminRole->id)
            ->where('permission_id', $permId)
            ->exists();

        if (!$exists) {
            DB::table('permission_role')->insert([
                'role_id' => $adminRole->id,
                'permission_id' => $permId,
            ]);
            echo "✓ Permission '$name' ditambahkan ke role admin\n";
        } else {
            echo "✓ Permission '$name' sudah ada di role admin\n";
        }
    }

    echo "\n===========================================\n";
    echo "SELESAI!\n";
    echo "===========================================\n";
    echo "\nSilakan:\n";
    echo "1. Logout dari aplikasi\n";
    echo "2. Login kembali\n";
    echo "3. Menu OB Bongkar akan muncul di sidebar\n";
    echo "\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
