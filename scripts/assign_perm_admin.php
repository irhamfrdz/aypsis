<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$admin = User::where('username', 'admin')->first();
if (!$admin) {
    echo "Admin user not found\n"; exit(1);
}

$perm = \App\Models\Permission::where('name', 'master-pembayaran-pranota-supir')->first();
if (!$perm) {
    echo "Permission master-pembayaran-pranota-supir not found\n"; exit(1);
}

$exists = \DB::table('user_permissions')->where(['user_id' => $admin->id, 'permission_id' => $perm->id])->exists();
if ($exists) {
    echo "Mapping already exists: user_id={$admin->id}, permission_id={$perm->id}\n";
} else {
    \DB::table('user_permissions')->insert(['user_id' => $admin->id, 'permission_id' => $perm->id]);
    echo "Inserted mapping: user_id={$admin->id}, permission_id={$perm->id}\n";
}

// Rebuild gate cache check
$gate = app(\Illuminate\Contracts\Auth\Access\Gate::class);
$can = $gate->check('master-pembayaran-pranota-supir', $admin);
echo "GATE_CHECK after insert: " . ($can ? 'yes' : 'no') . "\n";
