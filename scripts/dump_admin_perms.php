<?php
// Simple script to inspect admin user and permissions
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Gate;

$u = User::where('username', 'admin')->first();
if (!$u) {
    echo "NO_ADMIN_USER\n";
    exit(0);
}

echo "ADMIN_USER: id={$u->id}, username={$u->username}, name={$u->name}\n";

$perms = $u->permissions()->pluck('name')->toArray();
echo "ADMIN_PERMISSIONS (count=" . count($perms) . "):\n";
foreach ($perms as $p) {
    echo " - $p\n";
}

$p = Permission::where('name', 'master-pembayaran-pranota-supir')->first();
if ($p) {
    echo "PERMISSION_RECORD: id={$p->id}, name={$p->name}\n";
} else {
    echo "PERMISSION_RECORD: NOT_FOUND\n";
}

$rows = \DB::table('user_permissions')->where('user_id', $u->id)->get();
echo "USER_PERMISSIONS_TABLE rows: " . count($rows) . "\n";
foreach ($rows as $r) {
    echo " - user_id={$r->user_id}, permission_id={$r->permission_id}\n";
}

// Check roles for admin
$roles = $u->roles()->pluck('name')->toArray();
echo "ADMIN_ROLES: " . implode(',', $roles) . "\n";

// Proper Gate check for specific user
$gate = app(\Illuminate\Contracts\Auth\Access\Gate::class);
$can = $gate->forUser($u)->check('master-pembayaran-pranota-supir');
echo "GATE_CHECK master-pembayaran-pranota-supir for admin: " . ($can ? 'yes' : 'no') . "\n";

// End
