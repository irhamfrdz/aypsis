<?php

require_once 'vendor/autoload.php';

// Load Laravel configuration and bootstrap
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;

echo "Adding master kapal permissions...\n";

// Define master kapal permissions (sesuai dengan routes)
$permissions = [
    [
        'name' => 'master-kapal.view',
        'description' => 'View master kapal list'
    ],
    [
        'name' => 'master-kapal.create',
        'description' => 'Create new master kapal'
    ],
    [
        'name' => 'master-kapal.edit',
        'description' => 'Edit master kapal'
    ],
    [
        'name' => 'master-kapal.delete',
        'description' => 'Delete master kapal'
    ],
    [
        'name' => 'master-kapal.print',
        'description' => 'Print master kapal'
    ],
    [
        'name' => 'master-kapal.export',
        'description' => 'Export master kapal'
    ]
];

$created = 0;
$existing = 0;

foreach ($permissions as $permissionData) {
    $permission = Permission::firstOrCreate(
        ['name' => $permissionData['name']],
        [
            'description' => $permissionData['description'],
            'created_at' => now(),
            'updated_at' => now()
        ]
    );

    if ($permission->wasRecentlyCreated) {
        echo "✓ Created permission: {$permissionData['name']}\n";
        $created++;
    } else {
        echo "- Permission already exists: {$permissionData['name']}\n";
        $existing++;
    }
}

echo "\nSummary:\n";
echo "Created: $created permissions\n";
echo "Already existing: $existing permissions\n";
echo "Total master kapal permissions: " . ($created + $existing) . "\n";

echo "\nMaster kapal permissions have been successfully added to the database!\n";
