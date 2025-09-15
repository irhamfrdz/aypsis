<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Route;

echo "=== DIAGNOSIS: USER TEST2 PRANOTA ACCESS ISSUE ===\n\n";

// 1. Get user test2
$user = User::where('username', 'test2')->with('permissions')->first();
if (!$user) {
    echo "❌ User test2 not found!\n";
    exit(1);
}

echo "👤 USER TEST2 INFO:\n";
echo "  - Name: {$user->name}\n";
echo "  - Username: {$user->username}\n";
echo "  - ID: {$user->id}\n";
echo "  - Is Admin: " . ($user->hasRole('admin') ? '✅ YES' : '❌ NO') . "\n\n";

// 2. Show user's pranota-related permissions
$pranotaPerms = $user->permissions->filter(function($perm) {
    return strpos($perm->name, 'pranota') !== false;
});

echo "📋 USER TEST2 PRANOTA PERMISSIONS:\n";
if ($pranotaPerms->isEmpty()) {
    echo "❌ No pranota permissions found!\n";
} else {
    foreach ($pranotaPerms as $perm) {
        echo "  ✅ {$perm->name}\n";
    }
}
echo "\n";

// 3. Check pranota routes and their requirements
echo "🛣️  PRANOTA ROUTES ANALYSIS:\n";
$pranotaRoutes = [
    'pranota-supir.index' => 'master-pranota-supir',
    'pranota-supir.create' => 'master-pranota-supir',
    'pranota-supir.show' => 'master-pranota-supir',
    'pranota-supir.edit' => 'master-pranota-supir',
    'pranota-supir.store' => 'master-pranota-supir',
    'pranota-supir.update' => 'master-pranota-supir',
    'pranota-supir.destroy' => 'master-pranota-supir',
];

foreach ($pranotaRoutes as $routeName => $requiredPerm) {
    if (Route::has($routeName)) {
        $hasRequiredPerm = $user->hasPermissionTo($requiredPerm);
        $hasSimilarPerm = $user->hasPermissionLike('pranota-supir');

        echo "  {$routeName}:\n";
        echo "    - Required: {$requiredPerm}\n";
        echo "    - User has required: " . ($hasRequiredPerm ? '✅ YES' : '❌ NO') . "\n";
        echo "    - User has similar: " . ($hasSimilarPerm ? '✅ YES' : '❌ NO') . "\n";
        echo "    - Access status: " . (($hasRequiredPerm || $user->hasRole('admin')) ? '✅ ALLOWED' : '❌ BLOCKED') . "\n";
        echo "\n";
    } else {
        echo "  ❌ Route {$routeName} does not exist\n\n";
    }
}

// 4. Check sidebar menu visibility
echo "🎛️  SIDEBAR MENU VISIBILITY:\n";
$isAdmin = $user->hasRole('admin');
$canMasterPranotaSupir = $user->hasPermissionTo('master-pranota-supir');
$hasPranotaLike = $user->hasPermissionLike('pranota-supir');

$menuVisible = $isAdmin || $canMasterPranotaSupir || $hasPranotaLike;
echo "  - Is Admin: " . ($isAdmin ? '✅ YES' : '❌ NO') . "\n";
echo "  - Has master-pranota-supir: " . ($canMasterPranotaSupir ? '✅ YES' : '❌ NO') . "\n";
echo "  - Has pranota-supir.* permissions: " . ($hasPranotaLike ? '✅ YES' : '❌ NO') . "\n";
echo "  - Menu should be visible: " . ($menuVisible ? '✅ YES' : '❌ NO') . "\n\n";

// 5. Diagnosis and solutions
echo "🔍 DIAGNOSIS:\n";
if (!$menuVisible) {
    echo "❌ The pranota menu should NOT be visible in sidebar for user test2\n";
    echo "   because they don't have the required permission.\n\n";
} else {
    echo "✅ The pranota menu SHOULD be visible in sidebar for user test2\n";
    echo "   because hasPermissionLike('pranota-supir') returns true.\n\n";
}

echo "🚨 ROOT CAUSE OF 403 ERROR:\n";
echo "User test2 has permissions like 'pranota-supir.index', 'pranota-supir.create', etc.\n";
echo "But the routes require permission 'master-pranota-supir' (exact match).\n";
echo "This creates a mismatch between what user has vs what routes require.\n\n";

echo "💡 SOLUTIONS:\n\n";

echo "SOLUTION 1 - ADD MISSING PERMISSION:\n";
echo "Assign permission 'master-pranota-supir' to user test2\n\n";

echo "SOLUTION 2 - MODIFY ROUTE MIDDLEWARE:\n";
echo "Change route middleware to accept 'pranota-supir.*' permissions\n\n";

echo "SOLUTION 3 - USE PERMISSION TEMPLATES:\n";
echo "Apply a template that includes 'master-pranota-supir'\n\n";

echo "SOLUTION 4 - MODIFY AUTHORIZATION LOGIC:\n";
echo "Update AuthServiceProvider to map pranota-supir.* to master-pranota-supir\n\n";

echo "🔧 RECOMMENDED IMMEDIATE FIX:\n";
echo "Add permission 'master-pranota-supir' to user test2 via bulk management\n\n";

echo "=== END DIAGNOSIS ===\n";
