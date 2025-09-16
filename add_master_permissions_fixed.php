<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

echo "🔧 Adding missing master data view permissions to user test4...\n\n";

$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found!\n";
    exit(1);
}

echo "✅ Found user test4 (ID: {$user->id})\n\n";

$permissionsToAdd = [
    'master-karyawan.view',
    'master-user.view',
    'master-kontainer.view',
    'master-tujuan.view',
    'master-kegiatan.view',
    'master-permission.view',
    'master-mobil.view'
];

$addedCount = 0;
foreach ($permissionsToAdd as $permName) {
    $permission = Permission::where('name', $permName)->first();
    if ($permission) {
        // Check if user already has this permission
        $existing = DB::table('user_permissions')
            ->where('user_id', $user->id)
            ->where('permission_id', $permission->id)
            ->first();

        if (!$existing) {
            // Add the permission
            DB::table('user_permissions')->insert([
                'user_id' => $user->id,
                'permission_id' => $permission->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "✅ Added permission: {$permName}\n";
            $addedCount++;
        } else {
            echo "ℹ️ Already has permission: {$permName}\n";
        }
    } else {
        echo "❌ Permission not found in database: {$permName}\n";
    }
}

echo "\n🎉 Permission update completed!\n";
echo "📊 Added {$addedCount} new permissions to user test4\n";

// Verify the permissions
echo "\n🔍 Verifying permissions:\n";
foreach ($permissionsToAdd as $permName) {
    $permission = Permission::where('name', $permName)->first();
    if ($permission) {
        $hasPermission = DB::table('user_permissions')
            ->where('user_id', $user->id)
            ->where('permission_id', $permission->id)
            ->exists();
        echo "  - {$permName}: " . ($hasPermission ? '✅ YES' : '❌ NO') . "\n";
    }
}

echo "\n💡 Now user test4 should be able to see the Master Data menu in the sidebar!\n";
echo "🔄 Please refresh the page or clear cache if needed.\n";
