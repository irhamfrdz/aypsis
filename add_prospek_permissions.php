<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Permission;
use App\Models\User;

echo "🔧 Adding Prospek Permissions...\n\n";

// Permissions yang akan ditambahkan
$permissions = [
    [
        'name' => 'prospek-view',
        'description' => 'View prospek data'
    ],
    [
        'name' => 'prospek-edit',
        'description' => 'Edit prospek data'
    ],
    [
        'name' => 'prospek-delete',
        'description' => 'Delete prospek data'
    ],
];

DB::beginTransaction();

try {
    $addedCount = 0;
    $existingCount = 0;

    foreach ($permissions as $permData) {
        $permission = Permission::where('name', $permData['name'])->first();
        
        if (!$permission) {
            Permission::create([
                'name' => $permData['name'],
                'description' => $permData['description'],
                'guard_name' => 'web'
            ]);
            echo "✅ Created permission: {$permData['name']}\n";
            $addedCount++;
        } else {
            echo "ℹ️  Permission already exists: {$permData['name']}\n";
            $existingCount++;
        }
    }

    // Assign permissions to admin user (optional)
    $adminUser = User::where('username', 'admin')->first();
    if ($adminUser) {
        echo "\n📝 Assigning permissions to admin user...\n";
        foreach ($permissions as $permData) {
            $permission = Permission::where('name', $permData['name'])->first();
            if ($permission) {
                // Check if already assigned
                $exists = DB::table('user_permissions')
                    ->where('user_id', $adminUser->id)
                    ->where('permission_id', $permission->id)
                    ->exists();
                
                if (!$exists) {
                    DB::table('user_permissions')->insert([
                        'user_id' => $adminUser->id,
                        'permission_id' => $permission->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    echo "✅ Assigned {$permData['name']} to admin\n";
                } else {
                    echo "ℹ️  Admin already has {$permData['name']}\n";
                }
            }
        }
    }

    DB::commit();
    
    echo "\n🎉 Permission setup completed!\n";
    echo "📊 Created: {$addedCount} | Already exists: {$existingCount}\n";
    echo "\n✨ All prospek permissions are now available in the system.\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
