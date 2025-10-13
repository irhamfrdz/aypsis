<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

echo "=== Adding All Missing Dash Format Permissions ===\n";

$user = User::find(1);
echo "User: {$user->username}\n\n";

// List of all missing dash format permissions
$missingDashPermissions = [
    'master-tujuan-kegiatan-utama-view',
    'master-tujuan-kegiatan-utama-create',
    'master-tujuan-kegiatan-utama-update',
    'master-tujuan-kegiatan-utama-delete',
    'master-tujuan-kegiatan-utama-print',
    'master-tujuan-kegiatan-utama-export'
];

$descriptions = [
    'master-tujuan-kegiatan-utama-view' => 'Melihat Master Tujuan Kegiatan Utama',
    'master-tujuan-kegiatan-utama-create' => 'Membuat Master Tujuan Kegiatan Utama',
    'master-tujuan-kegiatan-utama-update' => 'Mengupdate Master Tujuan Kegiatan Utama',
    'master-tujuan-kegiatan-utama-delete' => 'Menghapus Master Tujuan Kegiatan Utama',
    'master-tujuan-kegiatan-utama-print' => 'Print Master Tujuan Kegiatan Utama',
    'master-tujuan-kegiatan-utama-export' => 'Export Master Tujuan Kegiatan Utama'
];

$createdCount = 0;
$assignedCount = 0;

foreach ($missingDashPermissions as $permissionName) {
    $permission = Permission::where('name', $permissionName)->first();
    
    if (!$permission) {
        // Create the permission
        $permission = Permission::create([
            'name' => $permissionName,
            'description' => $descriptions[$permissionName]
        ]);
        echo "✅ Created permission: {$permissionName}\n";
        $createdCount++;
    }
    
    // Check if user already has this permission
    $existing = DB::table('user_permissions')
        ->where('user_id', $user->id)
        ->where('permission_id', $permission->id)
        ->first();

    if (!$existing) {
        DB::table('user_permissions')->insert([
            'user_id' => $user->id,
            'permission_id' => $permission->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✅ Assigned to admin: {$permissionName}\n";
        $assignedCount++;
    } else {
        echo "⚠️  Admin already has: {$permissionName}\n";
    }
}

echo "\n📊 Summary:\n";
echo "- Created: {$createdCount} permissions\n";
echo "- Assigned: {$assignedCount} permissions to admin\n";

echo "\n=== Final Verification ===\n";
foreach ($missingDashPermissions as $perm) {
    $hasPerm = $user->can($perm);
    echo ($hasPerm ? "✅" : "❌") . " {$perm}: " . ($hasPerm ? "YES" : "NO") . "\n";
}

echo "\n🎉 All dash format permissions are now available!\n";
echo "Try accessing: http://localhost/master/tujuan-kegiatan-utama\n";