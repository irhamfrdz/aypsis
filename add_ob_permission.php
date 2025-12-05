<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

try {
    echo "🚀 Starting OB Permission Installation...\n\n";

    // Start transaction
    DB::beginTransaction();

    // 1. Create OB permission
    echo "📝 Creating OB permission...\n";
    
    $obPermission = Permission::firstOrCreate(
        ['name' => 'ob-view'],
        [
            'description' => 'View OB (Ocean Bunker) - pilih kapal dan voyage',
            'category' => 'OB',
            'created_at' => now(),
            'updated_at' => now()
        ]
    );

    if ($obPermission->wasRecentlyCreated) {
        echo "   ✅ Created new permission: ob-view\n";
    } else {
        echo "   ℹ️  Permission already exists: ob-view\n";
    }

    // 2. Assign to admin users (users with most permissions)
    echo "\n👥 Assigning OB permission to admin users...\n";
    
    // Find users who likely are admins (have many permissions)
    $adminUsers = User::withCount('permissions')
        ->having('permissions_count', '>', 50) // Users with more than 50 permissions are likely admins
        ->get();

    $assignedCount = 0;
    foreach ($adminUsers as $user) {
        // Check if user already has this permission
        if (!$user->permissions()->where('name', 'ob-view')->exists()) {
            $user->permissions()->attach($obPermission->id);
            $assignedCount++;
            echo "   ✅ Assigned to user: {$user->username}\n";
        } else {
            echo "   ℹ️  User already has permission: {$user->username}\n";
        }
    }

    // 3. Also assign to specific admin usernames (common admin usernames)
    $commonAdminUsernames = ['admin', 'administrator', 'superuser', 'root'];
    $specificAdmins = User::whereIn('username', $commonAdminUsernames)->get();
    
    foreach ($specificAdmins as $user) {
        if (!$user->permissions()->where('name', 'ob-view')->exists()) {
            $user->permissions()->attach($obPermission->id);
            $assignedCount++;
            echo "   ✅ Assigned to admin user: {$user->username}\n";
        }
    }

    echo "\n📊 Summary:\n";
    echo "   • Permission created/verified: ob-view\n";
    echo "   • Users assigned: {$assignedCount}\n";
    echo "   • Total admin users found: " . ($adminUsers->count() + $specificAdmins->count()) . "\n";

    // 4. Verify installation
    echo "\n🔍 Verifying installation...\n";
    
    $permission = Permission::where('name', 'ob-view')->first();
    if ($permission) {
        $userCount = $permission->users()->count();
        echo "   ✅ Permission exists with ID: {$permission->id}\n";
        echo "   ✅ Assigned to {$userCount} users\n";
    } else {
        throw new Exception("Permission verification failed!");
    }

    // Commit transaction
    DB::commit();
    
    echo "\n🎉 OB Permission installation completed successfully!\n\n";
    
    echo "📋 Next Steps:\n";
    echo "   1. Go to Master User management\n";
    echo "   2. Edit any user to assign OB permission manually if needed\n";
    echo "   3. Test OB access at /ob route\n";
    echo "   4. The OB permission allows users to:\n";
    echo "      - Access OB ship/voyage selection page\n";
    echo "      - Select ships and voyages for OB operations\n";
    echo "      - Navigate to Tagihan OB and Pranota OB\n\n";

} catch (Exception $e) {
    // Rollback on error
    DB::rollBack();
    
    echo "❌ Error during installation: " . $e->getMessage() . "\n";
    echo "📋 Installation rolled back.\n\n";
    
    exit(1);
}