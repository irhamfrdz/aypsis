<?php

require_once 'vendor/autoload.php';

// Load Laravel configuration and bootstrap
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;
use App\Models\User;

echo "=====================================================\n";
echo "Adding Tanda Terima Bongkaran permissions...\n";
echo "=====================================================\n\n";

// Define tanda terima bongkaran permissions (sesuai dengan routes)
$permissions = [
    [
        'name' => 'tanda-terima-bongkaran-view',
        'description' => 'View tanda terima bongkaran list'
    ],
    [
        'name' => 'tanda-terima-bongkaran-create',
        'description' => 'Create new tanda terima bongkaran'
    ],
    [
        'name' => 'tanda-terima-bongkaran-update',
        'description' => 'Update tanda terima bongkaran'
    ],
    [
        'name' => 'tanda-terima-bongkaran-delete',
        'description' => 'Delete tanda terima bongkaran'
    ],
    [
        'name' => 'tanda-terima-bongkaran-print',
        'description' => 'Print tanda terima bongkaran'
    ],
    [
        'name' => 'tanda-terima-bongkaran-export',
        'description' => 'Export tanda terima bongkaran'
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
        $created++;
        echo "✓ Created: {$permissionData['name']}\n";
    } else {
        $existing++;
        echo "→ Already exists: {$permissionData['name']}\n";
    }
}

echo "\n=====================================================\n";
echo "Summary:\n";
echo "- Created: {$created} permissions\n";
echo "- Already exists: {$existing} permissions\n";
echo "- Total: " . count($permissions) . " permissions\n";
echo "=====================================================\n\n";

// Optional: Add permissions to admin user
echo "Do you want to add these permissions to admin users? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);

if (trim($line) == 'y' || trim($line) == 'Y') {
    echo "\nAdding permissions to admin users...\n";
    
    // Find admin users (customize this query based on your system)
    $adminUsers = User::where('username', 'admin')
                       ->orWhere('username', 'like', '%admin%')
                       ->get();
    
    if ($adminUsers->isEmpty()) {
        echo "⚠ No admin users found!\n";
        echo "Please specify admin user IDs manually (comma-separated) or press Enter to skip: ";
        $line = fgets($handle);
        $adminIds = array_filter(array_map('trim', explode(',', trim($line))));
        
        if (!empty($adminIds)) {
            $adminUsers = User::whereIn('id', $adminIds)->get();
        }
    }
    
    $usersUpdated = 0;
    foreach ($adminUsers as $user) {
        echo "Processing user: {$user->username} (ID: {$user->id})\n";
        
        foreach ($permissions as $permissionData) {
            $permission = Permission::where('name', $permissionData['name'])->first();
            
            if ($permission && !$user->permissions->contains($permission->id)) {
                $user->permissions()->attach($permission->id);
                echo "  ✓ Added: {$permissionData['name']}\n";
            } else {
                echo "  → Already has: {$permissionData['name']}\n";
            }
        }
        
        $usersUpdated++;
        echo "\n";
    }
    
    echo "=====================================================\n";
    echo "Updated {$usersUpdated} admin user(s) with tanda terima bongkaran permissions.\n";
    echo "=====================================================\n";
} else {
    echo "\nSkipped adding permissions to admin users.\n";
}

fclose($handle);

echo "\n✅ Script completed successfully!\n";
echo "You can now use tanda terima bongkaran permissions in your system.\n\n";
