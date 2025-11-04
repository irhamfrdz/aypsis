<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// Setup database connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'aypsis',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    // Create permissions for Tarif Batam
    $permissions = [
        'tarif-batam.view',
        'tarif-batam.create', 
        'tarif-batam.edit',
        'tarif-batam.delete'
    ];

    foreach ($permissions as $permission) {
        Permission::firstOrCreate(['name' => $permission]);
        echo "Permission '{$permission}' created/exists\n";
    }

    // Assign permissions to admin role
    $adminRole = Role::where('name', 'admin')->first();
    if ($adminRole) {
        $adminRole->givePermissionTo($permissions);
        echo "Permissions assigned to admin role\n";
    } else {
        echo "Admin role not found\n";
    }

    // Assign view permission to user role if exists
    $userRole = Role::where('name', 'user')->first();
    if ($userRole) {
        $userRole->givePermissionTo(['tarif-batam.view']);
        echo "View permission assigned to user role\n";
    }

    echo "Tarif Batam permissions created and assigned successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}