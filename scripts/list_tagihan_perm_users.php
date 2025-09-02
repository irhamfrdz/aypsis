<?php
// Usage: php scripts/list_tagihan_perm_users.php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Permission;

$perm = Permission::where('name', 'master-pranota-tagihan-kontainer')->first();
if (!$perm) {
    echo "Permission master-pranota-tagihan-kontainer not found\n";
    exit(0);
}

$rows = \DB::table('user_permissions')->where('permission_id', $perm->id)->get();
if (count($rows) === 0) {
    echo "No users have permission master-pranota-tagihan-kontainer\n";
    exit(0);
}

echo "Users with 'master-pranota-tagihan-kontainer' (permission_id={$perm->id}):\n";
foreach ($rows as $r) {
    $u = User::find($r->user_id);
    if ($u) {
        echo " - id={$u->id}, username={$u->username}, name={$u->name}\n";
    } else {
        echo " - id={$r->user_id} (user not found)\n";
    }
}
