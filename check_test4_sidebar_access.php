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
echo '=== SIDEBAR MENU ANALYSIS ===' . PHP_EOL;
echo 'Sidebar shows "Buat Permohonan" menu when:' . PHP_EOL;
echo '1. User is admin ($isAdmin = true)' . PHP_EOL;
echo '2. OR user has permission "permohonan" (auth()->user()->can("permohonan"))' . PHP_EOL;
echo PHP_EOL;

$hasSidebarAccess = $user->hasPermissionTo('permohonan');
$hasCreateAccess = $user->hasPermissionTo('permohonan.create');

echo 'Current status:' . PHP_EOL;
echo 'Can access sidebar menu: ' . ($hasSidebarAccess ? '✅ YES' : '❌ NO') . PHP_EOL;
echo 'Can create permohonan: ' . ($hasCreateAccess ? '✅ YES' : '❌ NO') . PHP_EOL;

if (!$hasSidebarAccess && $hasCreateAccess) {
    echo PHP_EOL . '🔍 ISSUE FOUND: User has create permission but missing sidebar permission!' . PHP_EOL;
    echo 'Solution: Add "permohonan" permission to user test4' . PHP_EOL;
} elseif ($hasSidebarAccess && $hasCreateAccess) {
    echo PHP_EOL . '✅ User should be able to see and access create menu' . PHP_EOL;
} elseif (!$hasSidebarAccess && !$hasCreateAccess) {
    echo PHP_EOL . '❌ User missing both sidebar and create permissions' . PHP_EOL;
} else {
    echo PHP_EOL . '⚠️  User has sidebar access but no create permission' . PHP_EOL;
}
