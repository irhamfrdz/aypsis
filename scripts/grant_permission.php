<?php
// Usage: php scripts/grant_permission.php [user_id]
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$userId = isset($argv[1]) && is_numeric($argv[1]) ? (int)$argv[1] : 1;

$u = App\Models\User::find($userId);
if (!$u) {
    echo "User with id={$userId} not found\n";
    exit(1);
}

$permissionName = 'master-pranota-tagihan-kontainer';

// Use the project's Permission model and pivot table 'user_permissions'
$perm = App\Models\Permission::firstOrCreate(
    ['name' => $permissionName],
    ['description' => 'Akses Master Pranota Tagihan Kontainer']
);

// Insert mapping into pivot if missing
$exists = \DB::table('user_permissions')->where(['user_id' => $u->id, 'permission_id' => $perm->id])->exists();
if ($exists) {
    echo "Mapping already exists: user_id={$u->id}, permission_id={$perm->id}\n";
    exit(0);
}

\DB::table('user_permissions')->insert(['user_id' => $u->id, 'permission_id' => $perm->id]);
echo "Inserted mapping: user_id={$u->id}, permission_id={$perm->id}\n";

// Optional gate check to verify
$gate = app(\Illuminate\Contracts\Auth\Access\Gate::class);
$can = $gate->check($permissionName, $u);
echo "GATE_CHECK after insert: " . ($can ? 'yes' : 'no') . "\n";
exit(0);
