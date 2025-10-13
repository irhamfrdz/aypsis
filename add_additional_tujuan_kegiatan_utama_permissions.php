<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

echo "=== Adding Additional Master Tujuan Kegiatan Utama Permissions to Admin User ===\n";

$user = User::find(1);
if (!$user) {
    echo "❌ User with ID 1 not found\n";
    exit;
}

echo "User: {$user->username}\n";

$additionalPerms = [
    'master-tujuan-kegiatan-utama.print',
    'master-tujuan-kegiatan-utama.export'
];

$grantedCount = 0;
foreach($additionalPerms as $permName) {
    $permission = Permission::where('name', $permName)->first();
    
    if ($permission) {
        // Check if admin already has this permission
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
            echo "✅ Added: {$permission->name}\n";
            $grantedCount++;
        } else {
            echo "⚠️  Already has: {$permission->name}\n";
        }
    } else {
        echo "❌ Permission not found: {$permName}\n";
    }
}

echo "\n📊 Summary: Granted {$grantedCount} new permissions\n";

echo "\n=== Verification ===\n";
$allTujuanKegiatanUtamaPerms = DB::table('user_permissions')
    ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
    ->where('user_permissions.user_id', $user->id)
    ->where('permissions.name', 'like', 'master-tujuan-kegiatan-utama%')
    ->pluck('permissions.name');

echo "User now has " . $allTujuanKegiatanUtamaPerms->count() . " master-tujuan-kegiatan-utama permissions:\n";
foreach($allTujuanKegiatanUtamaPerms as $perm) {
    echo "- $perm\n";
}
