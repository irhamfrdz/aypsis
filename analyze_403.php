<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;

echo "=== 403 UNAUTHORIZED ERROR ANALYSIS ===\n\n";

// 1. Check if there are any users in database
$userCount = User::count();
echo "👥 Total Users in Database: {$userCount}\n";

if ($userCount === 0) {
    echo "❌ No users found in database!\n";
    echo "This could cause 403 errors if trying to access protected routes.\n\n";
}

// 2. Check users with permissions
$usersWithPerms = User::whereHas('permissions')->count();
echo "👤 Users with Permissions: {$usersWithPerms}\n\n";

// 3. Check admin users
$adminUsers = User::whereHas('roles', function($q) {
    $q->where('name', 'admin');
})->count();
echo "👑 Admin Users: {$adminUsers}\n\n";

// 4. Check common problematic routes
echo "🔍 COMMON ROUTES THAT CAUSE 403 ERRORS:\n";
$problematicRoutes = [
    'master.user.index' => 'master-user',
    'master.user.create' => 'master-user',
    'master.user.edit' => 'master-user',
    'master.karyawan.index' => 'master-karyawan',
    'pranota-supir.index' => 'master-pranota-supir',
    'dashboard' => null,
];

foreach ($problematicRoutes as $routeName => $requiredPerm) {
    if (Route::has($routeName)) {
        echo "✅ Route exists: {$routeName}\n";
        if ($requiredPerm) {
            echo "   Required permission: {$requiredPerm}\n";
        }
    } else {
        echo "❌ Route missing: {$routeName}\n";
    }
}
echo "\n";

// 5. Check permission table
$permCount = DB::table('permissions')->count();
echo "📋 Total Permissions in Database: {$permCount}\n";

if ($permCount === 0) {
    echo "❌ No permissions found in database!\n";
    echo "This will cause 403 errors for all protected routes.\n\n";
}

// 6. Check user_permissions table
$userPermCount = DB::table('user_permissions')->count();
echo "🔗 User-Permission Relationships: {$userPermCount}\n\n";

// 7. List users and their permission counts
echo "📊 USERS AND THEIR PERMISSIONS:\n";
$users = User::with('permissions')->get();

foreach ($users as $user) {
    $permCount = $user->permissions->count();
    $isAdmin = $user->hasRole('admin');
    echo "  {$user->name} ({$user->username}):\n";
    echo "    - Permissions: {$permCount}\n";
    echo "    - Is Admin: " . ($isAdmin ? '✅ YES' : '❌ NO') . "\n";

    if ($permCount > 0) {
        echo "    - Sample permissions: ";
        $samplePerms = $user->permissions->take(3)->pluck('name')->toArray();
        echo implode(', ', $samplePerms);
        if ($permCount > 3) echo " ...";
        echo "\n";
    }
    echo "\n";
}

// 8. Most common causes and solutions
echo "🚨 MOST COMMON CAUSES OF 403 ERRORS:\n\n";

echo "1. ❌ USER NOT LOGGED IN\n";
echo "   - Solution: Login first, then access protected routes\n\n";

echo "2. ❌ USER HAS NO PERMISSIONS\n";
echo "   - Solution: Assign permissions using templates or manual assignment\n";
echo "   - Use: /master/user/bulk-manage to assign permissions\n\n";

echo "3. ❌ MISSING REQUIRED PERMISSION\n";
echo "   - Solution: Check what permission is required for the route\n";
echo "   - Add the missing permission to user\n\n";

echo "4. ❌ BROKEN MIDDLEWARE\n";
echo "   - Solution: Check middleware configuration in routes\n\n";

echo "5. ❌ DATABASE ISSUES\n";
echo "   - Solution: Run migrations, check table relationships\n\n";

echo "💡 QUICK FIXES:\n";
echo "1. Make a user admin: Assign 'admin' role\n";
echo "2. Use permission templates: Apply 'staff' or 'supervisor' template\n";
echo "3. Manual permission assignment: Add specific permissions needed\n";
echo "4. Check route middleware: Ensure correct permissions are required\n\n";

echo "🔧 DEBUGGING STEPS:\n";
echo "1. Login as admin user first\n";
echo "2. Try accessing the same route - should work\n";
echo "3. Login as the problematic user\n";
echo "4. Note the exact URL causing 403\n";
echo "5. Check what permission that route requires\n";
echo "6. Assign that permission to the user\n\n";

echo "=== END ANALYSIS ===\n";
