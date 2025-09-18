<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$users = DB::table('users')->limit(3)->get();
echo 'Checking user permissions for pranota-perbaikan-kontainer-create (ID: 401):' . PHP_EOL;
foreach($users as $user) {
    echo 'User ID: ' . $user->id . ' - Name: ' . ($user->name ?? 'No name') . PHP_EOL;

    // Check if user has pranota permission through role_user and permission_role tables
    $hasPermission = DB::table('permission_role')
        ->join('roles', 'permission_role.role_id', '=', 'roles.id')
        ->join('role_user', 'roles.id', '=', 'role_user.role_id')
        ->where('role_user.user_id', $user->id)
        ->where('permission_role.permission_id', 401) // pranota-perbaikan-kontainer-create
        ->exists();

    echo '  Has pranota-create permission: ' . ($hasPermission ? 'YES' : 'NO') . PHP_EOL;

    // Also check direct user permissions
    $directPermission = DB::table('user_permissions')
        ->where('user_id', $user->id)
        ->where('permission_id', 401)
        ->exists();

    echo '  Has direct pranota-create permission: ' . ($directPermission ? 'YES' : 'NO') . PHP_EOL;
    echo PHP_EOL;
}
?>