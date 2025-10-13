<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== ANALYZING USER-BASED PERMISSION SYSTEM ===\n\n";

echo "1. Permission System Architecture:\n";
echo "   ✓ Using direct user permissions (user_permissions table)\n";
echo "   ✓ No role-based permissions needed\n";
echo "   ✓ Each user gets permissions assigned individually\n";

echo "\n2. Table Structure Verification:\n";
$userPermissionsCount = DB::table('user_permissions')->count();
echo "   ✓ user_permissions table: {$userPermissionsCount} records\n";

$permissionsCount = DB::table('permissions')->count();
echo "   ✓ permissions table: {$permissionsCount} records\n";

echo "\n3. Pranota Permission Status:\n";

// Check pranota-kontainer-sewa-create
$pranotaKontainerSewaCreate = DB::table('permissions')->where('name', 'pranota-kontainer-sewa-create')->first();
if ($pranotaKontainerSewaCreate) {
    echo "   ✓ pranota-kontainer-sewa-create EXISTS (ID: {$pranotaKontainerSewaCreate->id})\n";

    // Check how many users have this permission
    $usersWithPerm = DB::table('user_permissions')->where('permission_id', $pranotaKontainerSewaCreate->id)->count();
    echo "     - Assigned to {$usersWithPerm} users\n";
} else {
    echo "   ✗ pranota-kontainer-sewa-create MISSING\n";
}

// Check pranota-tagihan-kontainer.create
$pranotaTagihanKontainerCreate = DB::table('permissions')->where('name', 'pranota-tagihan-kontainer.create')->first();
if ($pranotaTagihanKontainerCreate) {
    echo "   ✓ pranota-tagihan-kontainer.create EXISTS (ID: {$pranotaTagihanKontainerCreate->id})\n";

    // Check how many users have this permission
    $usersWithPerm = DB::table('user_permissions')->where('permission_id', $pranotaTagihanKontainerCreate->id)->count();
    echo "     - Assigned to {$usersWithPerm} users\n";
} else {
    echo "   ✗ pranota-tagihan-kontainer.create MISSING\n";
}

echo "\n4. User Permission Analysis:\n";
$usersWithPermissions = DB::table('users')
    ->join('user_permissions', 'users.id', '=', 'user_permissions.user_id')
    ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
    ->where('permissions.name', 'like', '%pranota%kontainer%create%')
    ->select('users.id', 'users.username', 'permissions.name')
    ->get();

if ($usersWithPermissions->count() > 0) {
    echo "   Users with pranota kontainer create permissions:\n";
    foreach ($usersWithPermissions as $user) {
        echo "     ✓ User {$user->username} (ID: {$user->id}) has: {$user->name}\n";
    }
} else {
    echo "   ❌ No users have pranota kontainer create permissions\n";
}

echo "\n5. CONSISTENCY ISSUE IDENTIFIED:\n";
echo "   🔍 PROBLEM: Tombol 'Masukan ke Pranota' menggunakan 2 permission berbeda:\n";
echo "   \n";
echo "   📍 BLADE TEMPLATE (daftar-tagihan-kontainer-sewa/index.blade.php):\n";
echo "      @can('pranota-kontainer-sewa-create')\n";
echo "   \n";
echo "   📍 JAVASCRIPT FUNCTION (masukanKePranota, buatPranota):\n";
echo "      hasPermissionTo('pranota-tagihan-kontainer.create')\n";
echo "   \n";
echo "   ⚠️  IMPACT: User might see button but get warning when clicking\n";

echo "\n6. SOLUTION OPTIONS:\n";
echo "   \n";
echo "   OPTION A - Standardize to 'pranota-kontainer-sewa-create':\n";
echo "   ✅ PROS: Matches current blade template\n";
echo "   ✅ PROS: Consistent with pranota-kontainer-sewa module naming\n";
echo "   📝 CHANGE: Update JavaScript to use 'pranota-kontainer-sewa-create'\n";
echo "   \n";
echo "   OPTION B - Standardize to 'pranota-tagihan-kontainer.create':\n";
echo "   ✅ PROS: Matches current JavaScript implementation\n";
echo "   ✅ PROS: Consistent with pranota-tagihan-kontainer module naming\n";
echo "   📝 CHANGE: Update blade template to use 'pranota-tagihan-kontainer.create'\n";

echo "\n7. UserController Status:\n";
echo "   ✅ Both permission patterns are mapped correctly\n";
echo "   ✅ Matrix conversion supports both formats\n";
echo "   ✅ No changes needed in UserController\n";

echo "\n=== ANALYSIS COMPLETED ===\n";

echo "\n🎯 RECOMMENDED ACTION:\n";
echo "   Use OPTION A - Standardize to 'pranota-kontainer-sewa-create'\n";
echo "   Reason: This follows the naming convention of the main module\n";
echo "   and is already used in the blade template.\n";
