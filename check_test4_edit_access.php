<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

$user = User::where('username', 'test4')->first();
if (!$user) {
    echo '❌ User test4 not found' . PHP_EOL;
    exit;
}

echo '=== USER TEST4 CURRENT PERMISSIONS ===' . PHP_EOL;
echo 'User: ' . $user->username . PHP_EOL;
echo 'Email: ' . $user->email . PHP_EOL;
echo PHP_EOL;

echo 'Current permissions:' . PHP_EOL;
foreach ($user->permissions as $perm) {
    echo '- ' . $perm->name . PHP_EOL;
}

echo PHP_EOL;
echo '=== PERMISSION CHECKS ===' . PHP_EOL;
echo 'hasPermissionTo("permohonan"): ' . ($user->hasPermissionTo('permohonan') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'hasPermissionTo("permohonan.index"): ' . ($user->hasPermissionTo('permohonan.index') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'hasPermissionTo("permohonan.create"): ' . ($user->hasPermissionTo('permohonan.create') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'hasPermissionTo("permohonan.edit"): ' . ($user->hasPermissionTo('permohonan.edit') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'hasPermissionTo("permohonan.delete"): ' . ($user->hasPermissionTo('permohonan.delete') ? '✅ YES' : '❌ NO') . PHP_EOL;

// Check for dash format permissions
echo PHP_EOL . '=== CHECKING DASH FORMAT PERMISSIONS ===' . PHP_EOL;
echo 'hasPermissionTo("permohonan-edit"): ' . ($user->hasPermissionTo('permohonan-edit') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'hasPermissionTo("permohonan-update"): ' . ($user->hasPermissionTo('permohonan-update') ? '✅ YES' : '❌ NO') . PHP_EOL;

echo PHP_EOL;
echo '=== GATE CHECKS ===' . PHP_EOL;
Auth::login($user);
echo 'Gate::allows("permohonan"): ' . (\Illuminate\Support\Facades\Gate::allows('permohonan') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'Gate::allows("permohonan.index"): ' . (\Illuminate\Support\Facades\Gate::allows('permohonan.index') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'Gate::allows("permohonan.create"): ' . (\Illuminate\Support\Facades\Gate::allows('permohonan.create') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'Gate::allows("permohonan.edit"): ' . (\Illuminate\Support\Facades\Gate::allows('permohonan.edit') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'Gate::allows("permohonan.delete"): ' . (\Illuminate\Support\Facades\Gate::allows('permohonan.delete') ? '✅ YES' : '❌ NO') . PHP_EOL;
Auth::logout();

echo PHP_EOL;
echo '=== ROUTE ANALYSIS ===' . PHP_EOL;
$routes = app('router')->getRoutes();
foreach ($routes as $route) {
    if (str_contains($route->uri(), 'permohonan') && str_contains($route->uri(), 'edit')) {
        $middleware = $route->middleware();
        echo 'Route: ' . $route->uri() . PHP_EOL;
        echo 'Methods: ' . implode(', ', $route->methods()) . PHP_EOL;
        echo 'Middleware: ' . implode(', ', $middleware) . PHP_EOL;

        // Check if there's a 'can:' middleware
        foreach ($middleware as $mw) {
            if (str_starts_with($mw, 'can:')) {
                $requiredPermission = str_replace('can:', '', $mw);
                echo 'Required permission: ' . $requiredPermission . PHP_EOL;
            }
        }
        echo PHP_EOL;
        break; // Only check the first matching route
    }
}

echo '=== DIAGNOSIS ===' . PHP_EOL;
$hasSidebarAccess = $user->hasPermissionTo('permohonan');
$hasEditAccess = $user->hasPermissionTo('permohonan.edit');
$hasDashEdit = $user->hasPermissionTo('permohonan-edit');

echo 'Can access sidebar: ' . ($hasSidebarAccess ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'Can edit permohonan (dot format): ' . ($hasEditAccess ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'Can edit permohonan (dash format): ' . ($hasDashEdit ? '✅ YES' : '❌ NO') . PHP_EOL;

if (!$hasEditAccess && $hasDashEdit) {
    echo PHP_EOL . '🔍 ISSUE FOUND: User has dash format permission but route expects dot format!' . PHP_EOL;
    echo 'Solution: Replace permohonan-edit with permohonan.edit' . PHP_EOL;
} elseif ($hasEditAccess && !$hasDashEdit) {
    echo PHP_EOL . '✅ User permissions are correct for dot format' . PHP_EOL;
} elseif (!$hasEditAccess && !$hasDashEdit) {
    echo PHP_EOL . '❌ User missing edit permission in both formats' . PHP_EOL;
    echo 'Solution: Add permohonan.edit permission to user' . PHP_EOL;
} else {
    echo PHP_EOL . '⚠️  User has both formats - this might cause confusion' . PHP_EOL;
}
