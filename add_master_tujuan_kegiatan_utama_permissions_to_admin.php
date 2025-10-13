<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

echo "=== Adding Master Tujuan Kegiatan Utama Permissions to Admin User ===\n";

$user = User::find(1);
if (!$user) {
    echo "❌ User with ID 1 not found\n";
    exit;
}

echo "User: {$user->username}\n";

$permissions = Permission::where('name', 'like', 'master-tujuan-kegiatan-utama%')->get();
echo "Found " . $permissions->count() . " master-tujuan-kegiatan-utama permissions\n";

$grantedCount = 0;
foreach($permissions as $perm) {
    // Check if admin already has this permission
    $existing = DB::table('user_permissions')
        ->where('user_id', $user->id)
        ->where('permission_id', $perm->id)
        ->first();

    if (!$existing) {
        DB::table('user_permissions')->insert([
            'user_id' => $user->id,
            'permission_id' => $perm->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✅ Added: {$perm->name}\n";
        $grantedCount++;
    } else {
        echo "⚠️  Already has: {$perm->name}\n";
    }
}

echo "\n📊 Summary: Granted {$grantedCount} new permissions\n";

echo "\n=== Verification ===\n";
$userPermissions = DB::table('user_permissions')
    ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
    ->where('user_permissions.user_id', $user->id)
    ->where('permissions.name', 'like', 'master-tujuan-kegiatan-utama%')
    ->pluck('permissions.name');

echo "User now has " . $userPermissions->count() . " master-tujuan-kegiatan-utama permissions:\n";
foreach($userPermissions as $perm) {
    echo "- $perm\n";
}

echo "\n=== All Master Permissions for User ===\n";
$allMasterPermissions = DB::table('user_permissions')
    ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
    ->where('user_permissions.user_id', $user->id)
    ->where('permissions.name', 'like', 'master%')
    ->pluck('permissions.name');

echo "Total master permissions: " . $allMasterPermissions->count() . "\n";
foreach($allMasterPermissions as $perm) {
    echo "- $perm\n";
}
