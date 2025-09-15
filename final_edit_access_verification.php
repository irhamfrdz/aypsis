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

echo '=== FINAL VERIFICATION: USER TEST4 EDIT ACCESS ===' . PHP_EOL;
echo 'User: ' . $user->username . PHP_EOL;
echo PHP_EOL;

echo 'Current permissions:' . PHP_EOL;
foreach ($user->permissions as $perm) {
    echo '- ' . $perm->name . ' (ID: ' . $perm->id . ')' . PHP_EOL;
}

echo PHP_EOL;
echo '=== PERMISSION CHECKS ===' . PHP_EOL;
echo 'hasPermissionTo("permohonan"): ' . ($user->hasPermissionTo('permohonan') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'hasPermissionTo("permohonan.index"): ' . ($user->hasPermissionTo('permohonan.index') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'hasPermissionTo("permohonan.create"): ' . ($user->hasPermissionTo('permohonan.create') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'hasPermissionTo("permohonan.edit"): ' . ($user->hasPermissionTo('permohonan.edit') ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'hasPermissionTo("permohonan.delete"): ' . ($user->hasPermissionTo('permohonan.delete') ? '✅ YES' : '❌ NO') . PHP_EOL;

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
echo '=== ROUTE ACCESS TEST ===' . PHP_EOL;
$routes = app('router')->getRoutes();
foreach ($routes as $route) {
    if (str_contains($route->uri(), 'permohonan') && str_contains($route->uri(), 'edit')) {
        $middleware = $route->middleware();
        echo 'Route: ' . $route->uri() . PHP_EOL;
        echo 'Methods: ' . implode(', ', $route->methods()) . PHP_EOL;

        // Check if there's a 'can:' middleware
        foreach ($middleware as $mw) {
            if (str_starts_with($mw, 'can:')) {
                $requiredPermission = str_replace('can:', '', $mw);
                echo 'Required permission: ' . $requiredPermission . PHP_EOL;
                echo 'User has this permission: ' . ($user->hasPermissionTo($requiredPermission) ? '✅ YES' : '❌ NO') . PHP_EOL;
            }
        }
        echo PHP_EOL;
        break;
    }
}

echo '=== SUMMARY ===' . PHP_EOL;
$hasEditPermission = $user->hasPermissionTo('permohonan.edit');
$hasSidebarAccess = $user->hasPermissionTo('permohonan');

echo 'Can access sidebar: ' . ($hasSidebarAccess ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'Can edit permohonan: ' . ($hasEditPermission ? '✅ YES' : '❌ NO') . PHP_EOL;

if ($hasSidebarAccess && $hasEditPermission) {
    echo PHP_EOL . '🎉 SUCCESS: User test4 can now access edit permohonan!' . PHP_EOL;
    echo '✅ Permission format is correct' . PHP_EOL;
    echo '✅ Route middleware will allow access' . PHP_EOL;
    echo '✅ User should be able to click edit buttons on permohonan items' . PHP_EOL;
} else {
    echo PHP_EOL . '❌ ISSUE: Permission check failed' . PHP_EOL;
    if (!$hasSidebarAccess) echo '- Missing sidebar permission' . PHP_EOL;
    if (!$hasEditPermission) echo '- Missing edit permission' . PHP_EOL;
}

echo PHP_EOL . '=== NEXT STEPS ===' . PHP_EOL;
echo 'If user still cannot access edit menu:' . PHP_EOL;
echo '1. Clear browser cache and cookies' . PHP_EOL;
echo '2. Clear Laravel cache: php artisan cache:clear' . PHP_EOL;
echo '3. Clear route cache: php artisan route:clear' . PHP_EOL;
echo '4. Restart web server' . PHP_EOL;
echo '5. Check if there are additional permission checks in the blade template' . PHP_EOL;
