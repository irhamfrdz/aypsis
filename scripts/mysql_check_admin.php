<?php
// Check admin user in the application's configured DB (reads .env)
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

try {
    $u = User::where('username', 'admin')->first();
    if (!$u) {
        echo "NO_ADMIN_USER\n";
        exit(0);
    }

    echo "ADMIN_USER: id={$u->id}, username={$u->username}, name={$u->name}\n";
    echo "ROLES: " . ($u->roles()->pluck('name')->isEmpty() ? 'NONE' : $u->roles()->pluck('name')->implode(',')) . "\n";
    echo "PERMS: " . ($u->permissions()->pluck('name')->isEmpty() ? 'NONE' : $u->permissions()->pluck('name')->implode(',')) . "\n";

    $rows = \DB::table('user_permissions')->where('user_id', $u->id)->get();
    echo "USER_PERMISSIONS_TABLE rows: " . count($rows) . "\n";
    foreach ($rows as $r) {
        echo " - user_id={$r->user_id}, permission_id={$r->permission_id}\n";
    }

    $can = app(\Illuminate\Contracts\Auth\Access\Gate::class)->forUser($u)->check('master-pricelist-sewa-kontainer');
    echo "GATE_CHECK master-pricelist-sewa-kontainer for admin: " . ($can ? 'yes' : 'no') . "\n";
} catch (\Throwable $e) {
    echo 'ERR: ' . $e->getMessage() . "\n";
}
