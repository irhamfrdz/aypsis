<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

echo "=== CHECKING USER 'user_admin' ===\n";

$user = User::where('username', 'user_admin')->first();

if (!$user) {
    echo "ERROR: User 'user_admin' not found in database.\n";
    echo "Available users:\n";
    $users = User::select('id', 'username', 'name')->get();
    foreach ($users as $u) {
        echo "- ID: {$u->id}, Username: {$u->username}, Name: {$u->name}\n";
    }
    exit(1);
}

echo "User found:\n";
echo "- ID: {$user->id}\n";
echo "- Username: {$user->username}\n";
echo "- Name: {$user->name}\n";
echo "- Email: {$user->email}\n";

echo "\n=== CHECKING PERMISSIONS FOR 'user_admin' ===\n";

$permissions = $user->permissions()->get();
echo "Total permissions: " . $permissions->count() . "\n";

$hasKodeNomorView = false;
foreach ($permissions as $perm) {
    echo "- {$perm->name}\n";
    if ($perm->name === 'master-kode-nomor-view') {
        $hasKodeNomorView = true;
    }
}

echo "\n=== SPECIFIC CHECK FOR 'master-kode-nomor-view' ===\n";
if ($hasKodeNomorView) {
    echo "YES: User 'user_admin' has 'master-kode-nomor-view' permission.\n";
} else {
    echo "NO: User 'user_admin' does NOT have 'master-kode-nomor-view' permission.\n";

    // Check if permission exists in permissions table
    $perm = Permission::where('name', 'master-kode-nomor-view')->first();
    if ($perm) {
        echo "Permission exists in DB. Attaching to user...\n";
        $user->permissions()->attach($perm->id);
        echo "Attached successfully.\n";
    } else {
        echo "ERROR: Permission 'master-kode-nomor-view' not found in permissions table.\n";
    }
}

echo "\n=== GATE CHECK SIMULATION ===\n";
$gateAllows = $user->can('master-kode-nomor-view');
echo "Gate::allows('master-kode-nomor-view') for user_admin: " . ($gateAllows ? 'YES' : 'NO') . "\n";

echo "\n=== RECOMMENDATION ===\n";
if ($hasKodeNomorView && $gateAllows) {
    echo "User has permission and Gate allows. Menu should appear.\n";
    echo "Please reload the page and check for the red [DBG] badge next to 'Kode Nomor' in sidebar.\n";
    echo "Also check storage/logs/laravel.log for debug log entry.\n";
} else {
    echo "Permission was missing but now attached (if it existed).\n";
    echo "Please logout and login again as 'user_admin', then check sidebar.\n";
}
