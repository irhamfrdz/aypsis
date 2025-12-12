<?php

require_once 'vendor/autoload.php';

// Load Laravel configuration and bootstrap
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;
use App\Models\User;

echo "=====================================================\n";
echo "Adding Pembayaran Uang Muka Permissions...\n";
echo "=====================================================\n\n";

// Define pembayaran uang muka permissions
$permissions = [
    [
        'name' => 'pembayaran-uang-muka-view',
        'description' => 'View pembayaran uang muka list'
    ],
    [
        'name' => 'pembayaran-uang-muka-create',
        'description' => 'Create new pembayaran uang muka'
    ],
    [
        'name' => 'pembayaran-uang-muka-edit',
        'description' => 'Edit pembayaran uang muka'
    ],
    [
        'name' => 'pembayaran-uang-muka-delete',
        'description' => 'Delete pembayaran uang muka'
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
    
    // Find admin users
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
    echo "Updated {$usersUpdated} admin user(s) with pembayaran uang muka permissions.\n";
    echo "=====================================================\n";
} else {
    echo "\nSkipped adding permissions to admin users.\n";
}

fclose($handle);

echo "\n✅ Script completed successfully!\n";
echo "You can now use pembayaran uang muka permissions in your system.\n\n";
