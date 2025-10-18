<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;
use App\Models\User;

// Permissions untuk Audit Log
$auditPermissions = [
    'audit-log-view' => 'Melihat log aktivitas sistem',
    'audit-log-export' => 'Mengekspor log aktivitas ke CSV'
];

echo "Creating audit log permissions...\n";

foreach ($auditPermissions as $name => $description) {
    $permission = Permission::updateOrCreate(
        ['name' => $name],
        ['description' => $description]
    );

    echo "- {$name}: {$description}\n";
}

// Assign permissions ke admin
$adminUser = User::where('username', 'admin')->first();

if ($adminUser) {
    echo "\nAssigning audit log permissions to admin user...\n";

    foreach ($auditPermissions as $name => $description) {
        $permission = Permission::where('name', $name)->first();
        if ($permission) {
            // Check if permission already exists
            $exists = $adminUser->permissions()
                ->where('permission_id', $permission->id)
                ->exists();

            if (!$exists) {
                $adminUser->permissions()->attach($permission->id);
                echo "- Assigned: {$name}\n";
            } else {
                echo "- Already exists: {$name}\n";
            }
        }
    }
} else {
    echo "Admin user not found!\n";
}

echo "\nAudit log permissions setup completed!\n";
