<?php
// Script untuk menambahkan operational permissions ke admin user

require_once 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "🔧 Adding Operational Permissions to Admin User...\n\n";

    // Find admin user
    $adminUser = \App\Models\User::where('username', 'admin')->first();
    
    if (!$adminUser) {
        echo "❌ Admin user not found!\n";
        exit(1);
    }

    echo "✅ Found admin user (ID: {$adminUser->id})\n\n";

    // Get all operational permissions
    $operationalPermissions = \App\Models\Permission::where('name', 'like', 'order-management%')
        ->orWhere('name', 'like', 'surat-jalan%')
        ->orWhere('name', 'like', 'tanda-terima%')
        ->orWhere('name', 'like', 'gate-in%')
        ->orWhere('name', 'like', 'pranota-surat-jalan%')
        ->orWhere('name', 'like', 'approval-surat-jalan%')
        ->get();

    $addedCount = 0;
    $existingCount = 0;

    foreach ($operationalPermissions as $permission) {
        if (!$adminUser->permissions->contains($permission->id)) {
            $adminUser->permissions()->attach($permission->id);
            echo "✅ Added permission: {$permission->name}\n";
            $addedCount++;
        } else {
            echo "ℹ️  Admin already has permission: {$permission->name}\n";
            $existingCount++;
        }
    }

    echo "\n🎉 Operational permissions setup completed!\n";
    echo "📊 Added {$addedCount} permissions, {$existingCount} already existed\n";
    echo "📊 Total operational permissions: " . $operationalPermissions->count() . "\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}