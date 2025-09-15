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

echo '=== FINAL VERIFICATION: USER TEST4 PERMISSIONS ===' . PHP_EOL;
echo 'User: ' . $user->username . PHP_EOL;
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
echo '=== SIDEBAR & ROUTE ACCESS ===' . PHP_EOL;
$sidebarAccess = $user->hasPermissionTo('permohonan');
$createAccess = $user->hasPermissionTo('permohonan.create');
$indexAccess = $user->hasPermissionTo('permohonan.index');

echo 'Can see sidebar menu: ' . ($sidebarAccess ? '✅ YES' : '❌ NO') . ' (requires: permohonan)' . PHP_EOL;
echo 'Can access create page: ' . ($createAccess ? '✅ YES' : '❌ NO') . ' (requires: permohonan.create)' . PHP_EOL;
echo 'Can access index page: ' . ($indexAccess ? '✅ YES' : '❌ NO') . ' (requires: permohonan.index)' . PHP_EOL;

echo PHP_EOL;
if ($sidebarAccess && $createAccess && $indexAccess) {
    echo '🎉 SUCCESS: User test4 can now access all permohonan features!' . PHP_EOL;
    echo '✅ Sidebar menu will be visible' . PHP_EOL;
    echo '✅ "Buat Permohonan" link will be accessible' . PHP_EOL;
    echo '✅ All permohonan routes will work correctly' . PHP_EOL;
} else {
    echo '❌ ISSUE: Some permissions are still missing' . PHP_EOL;
    if (!$sidebarAccess) echo '- Missing sidebar permission (permohonan)' . PHP_EOL;
    if (!$createAccess) echo '- Missing create permission (permohonan.create)' . PHP_EOL;
    if (!$indexAccess) echo '- Missing index permission (permohonan.index)' . PHP_EOL;
}
