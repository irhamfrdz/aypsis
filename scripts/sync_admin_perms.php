<?php
// Sync all permissions to admin user and admin role. Uses app .env DB settings.
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Permission;
use App\Models\Role;

try {
    $u = User::where('username', 'admin')->first();
    if (!$u) {
        echo "NO_ADMIN_USER\n";
        exit(1);
    }

    $permIds = Permission::pluck('id')->toArray();
    if (empty($permIds)) {
        echo "NO_PERMISSIONS_DEFINED\n";
        exit(1);
    }

    // sync user permissions
    $u->permissions()->sync($permIds);

    // ensure admin role exists and has all permissions
    $adminRole = Role::firstOrCreate(['name' => 'admin'], ['description' => 'Administrator Sistem']);
    $adminRole->permissions()->sync($permIds);

    // ensure user has admin role
    $u->roles()->syncWithoutDetaching([$adminRole->id]);

    echo "SYNCED_ADMIN_PERMISSIONS: user_id={$u->id}, perms_count=" . count($permIds) . "\n";
    exit(0);
} catch (\Throwable $e) {
    echo 'ERR: ' . $e->getMessage() . "\n";
    exit(2);
}
