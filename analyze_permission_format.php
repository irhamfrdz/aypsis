<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo '=== PERMISSION FORMAT ANALYSIS ===' . PHP_EOL;

// Count permissions by format
$dashPermissions = Permission::where('name', 'like', '%-%')->count();
$dotPermissions = Permission::where('name', 'like', '%.%')->count();
$simplePermissions = Permission::where('name', 'not like', '%-%')->where('name', 'not like', '%.%')->count();

echo 'Permission count by format:' . PHP_EOL;
echo '- Dash format (permohonan-create): ' . $dashPermissions . PHP_EOL;
echo '- Dot format (permohonan.create): ' . $dotPermissions . PHP_EOL;
echo '- Simple format (permohonan): ' . $simplePermissions . PHP_EOL;

echo PHP_EOL;
echo '=== CHECKING WHICH FORMAT IS USED IN ROUTES ===' . PHP_EOL;

$routes = app('router')->getRoutes();
$routePermissions = [];

foreach ($routes as $route) {
    $middleware = $route->middleware();
    foreach ($middleware as $mw) {
        if (str_starts_with($mw, 'can:')) {
            $permission = str_replace('can:', '', $mw);
            if (str_contains($permission, 'permohonan')) {
                $routePermissions[] = $permission;
            }
        }
    }
}

$routePermissions = array_unique($routePermissions);
echo 'Permissions required by routes:' . PHP_EOL;
foreach ($routePermissions as $perm) {
    echo '- ' . $perm . PHP_EOL;
}

echo PHP_EOL;
echo '=== RECOMMENDATION ===' . PHP_EOL;

// Check which format is more prevalent
if ($dotPermissions > $dashPermissions) {
    echo '✅ RECOMMENDATION: Use DOT format (permohonan.create)' . PHP_EOL;
    echo 'Reason: More permissions use dot format and routes expect dot format' . PHP_EOL;
    echo PHP_EOL;
    echo 'SOLUTION: Update user test4 permission from permohonan-create to permohonan.create' . PHP_EOL;
} else {
    echo '✅ RECOMMENDATION: Use DASH format (permohonan-create)' . PHP_EOL;
    echo 'Reason: More permissions use dash format' . PHP_EOL;
    echo PHP_EOL;
    echo 'SOLUTION: Update route middleware to use dash format' . PHP_EOL;
}

// Check user test4 current permissions
$user = User::where('username', 'test4')->first();
if ($user) {
    echo PHP_EOL . '=== USER TEST4 CURRENT STATUS ===' . PHP_EOL;
    $userPerms = $user->permissions->pluck('name')->toArray();

    $hasDashCreate = in_array('permohonan-create', $userPerms);
    $hasDotCreate = in_array('permohonan.create', $userPerms);

    echo 'User has permohonan-create: ' . ($hasDashCreate ? '✅ YES' : '❌ NO') . PHP_EOL;
    echo 'User has permohonan.create: ' . ($hasDotCreate ? '✅ YES' : '❌ NO') . PHP_EOL;

    if ($hasDashCreate && !$hasDotCreate) {
        echo PHP_EOL . '🔧 ACTION NEEDED: Replace permohonan-create with permohonan.create' . PHP_EOL;
    } elseif (!$hasDashCreate && $hasDotCreate) {
        echo PHP_EOL . '🔧 ACTION NEEDED: Replace permohonan.create with permohonan-create' . PHP_EOL;
    } else {
        echo PHP_EOL . '✅ User permissions are correct' . PHP_EOL;
    }
}
