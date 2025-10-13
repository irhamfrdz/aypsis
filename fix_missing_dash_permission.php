<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

echo "=== FOUND THE ISSUE! Adding Missing Permission ===\n";

$user = User::find(1);
echo "User: {$user->username}\n\n";

echo "The issue is that user admin is missing: master-tujuan-kegiatan-utama-view (dash format)\n";
echo "But has: master-tujuan-kegiatan-utama.view (dot format)\n\n";

// Add the missing permission
$missingPermission = 'master-tujuan-kegiatan-utama-view';

$permission = Permission::where('name', $missingPermission)->first();
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
        echo "✅ Added missing permission: {$permission->name}\n";
    } else {
        echo "⚠️  User already has: {$permission->name}\n";
    }
} else {
    echo "❌ Permission not found in database: {$missingPermission}\n";
    echo "Creating the permission...\n";

    $newPermission = Permission::create([
        'name' => $missingPermission,
        'description' => 'Melihat Master Tujuan Kegiatan Utama (dash format)'
    ]);

    DB::table('user_permissions')->insert([
        'user_id' => $user->id,
        'permission_id' => $newPermission->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    echo "✅ Created and assigned permission: {$missingPermission}\n";
}

// Verify the fix
echo "\n=== Verification ===\n";
$hasDashPermission = $user->can('master-tujuan-kegiatan-utama-view');
$hasDotPermission = $user->can('master-tujuan-kegiatan-utama.view');

echo "master-tujuan-kegiatan-utama-view (dash): " . ($hasDashPermission ? "✅ YES" : "❌ NO") . "\n";
echo "master-tujuan-kegiatan-utama.view (dot): " . ($hasDotPermission ? "✅ YES" : "❌ NO") . "\n";

if ($hasDashPermission && $hasDotPermission) {
    echo "\n🎉 FIXED! Admin should now be able to access Master Tujuan Kegiatan Utama!\n";
} else {
    echo "\n❌ Still having issues. Need to investigate further.\n";
}
