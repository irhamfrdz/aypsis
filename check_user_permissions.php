<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => 'localhost',
    'database'  => 'aypsis',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    echo "🔍 Checking User Permissions\n";
    echo "===========================\n\n";

    // Find admin user
    $admin = Capsule::table('users')->where('email', 'admin@example.com')->first();

    if (!$admin) {
        // Try other common admin emails
        $admin = Capsule::table('users')->where('name', 'like', '%admin%')->first();
        if (!$admin) {
            $admin = Capsule::table('users')->first();
        }
    }

    if ($admin) {
        echo "Found user: {$admin->name} ({$admin->email})\n";
        echo "User ID: {$admin->id}\n\n";

        // Check permissions
        $perms = Capsule::table('model_has_permissions')
            ->where('model_id', $admin->id)
            ->where('model_type', 'App\\Models\\User')
            ->join('permissions', 'permissions.id', '=', 'model_has_permissions.permission_id')
            ->where('permissions.name', 'like', '%pergerakan-kapal%')
            ->select('permissions.name')
            ->get();

        echo "Pergerakan Kapal Permissions:\n";
        foreach($perms as $p) {
            echo "  ✅ {$p->name}\n";
        }

        if ($perms->isEmpty()) {
            echo "  ❌ No pergerakan-kapal permissions found!\n";

            // Check if user has roles
            $roles = Capsule::table('model_has_roles')
                ->where('model_id', $admin->id)
                ->where('model_type', 'App\\Models\\User')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->select('roles.name')
                ->get();

            echo "\nUser Roles:\n";
            foreach($roles as $r) {
                echo "  🎭 {$r->name}\n";

                // Check role permissions
                $rolePerms = Capsule::table('role_has_permissions')
                    ->where('role_id', Capsule::table('roles')->where('name', $r->name)->value('id'))
                    ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
                    ->where('permissions.name', 'like', '%pergerakan-kapal%')
                    ->select('permissions.name')
                    ->get();

                foreach($rolePerms as $rp) {
                    echo "    ✅ {$rp->name}\n";
                }
            }
        }

    } else {
        echo "❌ No admin user found!\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
