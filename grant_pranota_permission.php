<?php

// Grant pranota-supir permission to user test4
require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\DB;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔧 Granting pranota-supir permission to user test4\n";
echo "=================================================\n\n";

// Get user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "👤 User test4 found (ID: {$user->id})\n\n";

// Check if pranota-supir permission exists
$pranotaSupirPerm = DB::table('permissions')->where('name', 'pranota-supir')->first();

if (!$pranotaSupirPerm) {
    echo "❌ Permission 'pranota-supir' does not exist in database\n";
    echo "💡 Creating the permission first...\n";

    // Create the permission
    $newPermId = DB::table('permissions')->insertGetId([
        'name' => 'pranota-supir',
        'guard_name' => 'web',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    echo "✅ Created permission 'pranota-supir' with ID: {$newPermId}\n";
    $pranotaSupirPerm = DB::table('permissions')->find($newPermId);
}

// Check if user already has this permission
$hasPermission = DB::table('user_permissions')
    ->where('user_id', $user->id)
    ->where('permission_id', $pranotaSupirPerm->id)
    ->exists();

if ($hasPermission) {
    echo "✅ User test4 already has 'pranota-supir' permission\n";
} else {
    // Grant the permission
    DB::table('user_permissions')->insert([
        'user_id' => $user->id,
        'permission_id' => $pranotaSupirPerm->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    echo "✅ Successfully granted 'pranota-supir' permission to user test4\n";
}

// Also grant pranota-supir.view permission if it exists
$pranotaSupirViewPerm = DB::table('permissions')->where('name', 'pranota-supir.view')->first();
if ($pranotaSupirViewPerm) {
    $hasViewPermission = DB::table('user_permissions')
        ->where('user_id', $user->id)
        ->where('permission_id', $pranotaSupirViewPerm->id)
        ->exists();

    if (!$hasViewPermission) {
        DB::table('user_permissions')->insert([
            'user_id' => $user->id,
            'permission_id' => $pranotaSupirViewPerm->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✅ Also granted 'pranota-supir.view' permission to user test4\n";
    } else {
        echo "✅ User test4 already has 'pranota-supir.view' permission\n";
    }
}

// Verify the permissions
echo "\n🔍 Verifying permissions after granting:\n";
$userPermissions = DB::table('user_permissions')
    ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
    ->where('user_permissions.user_id', $user->id)
    ->where('permissions.name', 'like', '%pranota-supir%')
    ->select('permissions.name')
    ->get();

if ($userPermissions->count() > 0) {
    echo "  Current pranota-supir permissions for user test4:\n";
    foreach ($userPermissions as $perm) {
        echo "    ✅ {$perm->name}\n";
    }
} else {
    echo "  ❌ No pranota-supir permissions found\n";
}

echo "\n✅ Permission granting completed!\n";
echo "💡 Now the Pranota Supir menu should appear in the sidebar for user test4\n";
echo "💡 You may need to:\n";
echo "   1. Clear application cache: php artisan cache:clear\n";
echo "   2. Log out and log back in as test4\n";
echo "   3. Or restart the web server\n";

echo "\nTest completed: " . date('Y-m-d H:i:s') . "\n";
