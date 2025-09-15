<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Gate;

echo "=== 403 UNAUTHORIZED ERROR DIAGNOSTIC ===\n\n";

// 1. Check current user
$user = app('auth')->user();
if (!$user) {
    echo "❌ No authenticated user found!\n";
    echo "This could be the cause of 403 error - user not logged in.\n\n";
    exit(1);
}

echo "✅ Current User: {$user->name} ({$user->username}) - ID: {$user->id}\n\n";

// 2. Check user permissions
$permissions = $user->permissions;
echo "📋 USER PERMISSIONS ({$permissions->count()} total):\n";
if ($permissions->isEmpty()) {
    echo "❌ User has NO permissions assigned!\n";
    echo "This is likely the cause of 403 errors.\n\n";
} else {
    foreach ($permissions as $perm) {
        echo "  - {$perm->name}\n";
    }
}
echo "\n";

// 3. Check user roles
$roles = $user->roles;
echo "👤 USER ROLES ({$roles->count()} total):\n";
if ($roles->isEmpty()) {
    echo "❌ User has NO roles assigned!\n";
} else {
    foreach ($roles as $role) {
        echo "  - {$role->name}\n";
    }
}
echo "\n";

// 4. Check if user is admin
$isAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('admin');
echo "👑 IS ADMIN: " . ($isAdmin ? '✅ YES' : '❌ NO') . "\n\n";

// 5. Check common permission gates
echo "🔐 COMMON PERMISSION CHECKS:\n";
$commonPermissions = [
    'master-user',
    'master-karyawan',
    'master-pranota-supir',
    'master-pranota-tagihan-kontainer',
    'pranota-supir.index',
    'pranota-supir.create',
    'dashboard'
];

foreach ($commonPermissions as $perm) {
    $hasPerm = $user->hasPermissionTo($perm);
    $canAccess = Gate::forUser($user)->check($perm);
    echo "  {$perm}:\n";
    echo "    - hasPermissionTo(): " . ($hasPerm ? '✅ YES' : '❌ NO') . "\n";
    echo "    - Gate::check(): " . ($canAccess ? '✅ YES' : '❌ NO') . "\n";
}
echo "\n";

// 6. Check current route and middleware
$request = app('request');
$currentRoute = $request->route();

if ($currentRoute) {
    echo "🛣️  CURRENT ROUTE INFO:\n";
    echo "  - URI: {$currentRoute->uri()}\n";
    echo "  - Name: " . ($currentRoute->getName() ?: 'unnamed') . "\n";
    echo "  - Methods: " . implode(', ', $currentRoute->methods()) . "\n";

    $middleware = $currentRoute->middleware();
    if (!empty($middleware)) {
        echo "  - Middleware: " . implode(', ', $middleware) . "\n";
    } else {
        echo "  - Middleware: none\n";
    }
    echo "\n";
}

// 7. Check recent routes that might cause 403
echo "🔍 ROUTES THAT COMMONLY CAUSE 403 ERRORS:\n";
$problematicRoutes = [
    'master.user.index' => 'master-user',
    'master.user.create' => 'master-user',
    'master.user.edit' => 'master-user',
    'master.karyawan.index' => 'master-karyawan',
    'pranota-supir.index' => 'master-pranota-supir',
    'pranota-supir.create' => 'master-pranota-supir',
];

foreach ($problematicRoutes as $routeName => $requiredPerm) {
    if (Route::has($routeName)) {
        $hasAccess = $user->hasPermissionTo($requiredPerm) || $isAdmin;
        echo "  {$routeName}:\n";
        echo "    - Required: {$requiredPerm}\n";
        echo "    - Has Access: " . ($hasAccess ? '✅ YES' : '❌ NO') . "\n";
    }
}
echo "\n";

// 8. Recommendations
echo "💡 RECOMMENDATIONS:\n";

if ($permissions->isEmpty()) {
    echo "1. ❌ CRITICAL: User has no permissions!\n";
    echo "   - Assign appropriate permissions to this user\n";
    echo "   - Use bulk permission management or templates\n\n";
}

if (!$isAdmin && $permissions->count() < 3) {
    echo "2. ⚠️  WARNING: User has very few permissions\n";
    echo "   - Consider assigning more permissions or using a template\n\n";
}

echo "3. 🔧 QUICK FIXES:\n";
echo "   - Make user admin: Assign 'admin' role\n";
echo "   - Use permission template: Apply 'staff' or 'supervisor' template\n";
echo "   - Manual assignment: Add specific permissions needed\n\n";

echo "4. 🐛 DEBUG STEPS:\n";
echo "   - Check browser URL when 403 occurs\n";
echo "   - Note the exact route name\n";
echo "   - Verify required permissions for that route\n";
echo "   - Test with admin user to confirm route works\n\n";

echo "=== END DIAGNOSTIC ===\n";
