<?php
// Check permissions for user 'test2'
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Permission;

$username = 'test2';
$u = User::where('username', $username)->first();
if (!$u) {
    echo "USER_NOT_FOUND\n";
    exit(0);
}

echo "USER: id={$u->id}, username={$u->username}, name={$u->name}\n";
$perms = $u->permissions()->pluck('name')->toArray();
echo "PERMISSIONS (count=" . count($perms) . "):\n";
foreach ($perms as $p) {
    echo " - $p\n";
}

$rows = \DB::table('user_permissions')->where('user_id', $u->id)->get();
echo "USER_PERMISSIONS_TABLE rows: " . count($rows) . "\n";
foreach ($rows as $r) {
    echo " - user_id={$r->user_id}, permission_id={$r->permission_id}\n";
}

// Check gate evaluation for one known permission used in sidebar
$checkPerm = 'master-user';
$gate = app(\Illuminate\Contracts\Auth\Access\Gate::class);
$can = $gate->forUser($u)->check($checkPerm);
echo "GATE_CHECK {$checkPerm} for {$username}: " . ($can ? 'yes' : 'no') . "\n";


