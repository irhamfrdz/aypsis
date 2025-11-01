<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class VerifyAdminSeeder extends Seeder
{
    /**
     * Verify admin user and permissions
     */
    public function run(): void
    {
        $this->command->info("🔍 Verifying admin user setup...");

        // Check admin user
        $adminUser = User::where('username', 'admin')->first();
        
        if (!$adminUser) {
            $this->command->error("❌ Admin user not found!");
            return;
        }

        $this->command->info("✅ Admin user found:");
        $this->command->info("   - ID: {$adminUser->id}");
        $this->command->info("   - Username: {$adminUser->username}");
        $this->command->info("   - Status: {$adminUser->status}");
        $this->command->info("   - Created: {$adminUser->created_at}");

        // Check permissions
        $userPermissions = DB::table('user_permissions')
            ->where('user_id', $adminUser->id)
            ->count();

        $totalPermissions = DB::table('permissions')->count();

        $this->command->info("📊 Permission Summary:");
        $this->command->info("   - User permissions: {$userPermissions}");
        $this->command->info("   - Total available: {$totalPermissions}");
        
        if ($userPermissions == $totalPermissions) {
            $this->command->info("✅ Admin has ALL permissions assigned!");
        } else {
            $this->command->warn("⚠️  Admin missing some permissions!");
        }

        // Check specific pranota uang kenek permissions
        $kenekPermissions = DB::table('user_permissions')
            ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
            ->where('user_permissions.user_id', $adminUser->id)
            ->where('permissions.name', 'like', '%kenek%')
            ->get(['permissions.name']);

        $this->command->info("🎯 Pranota Uang Kenek permissions assigned:");
        foreach($kenekPermissions as $perm) {
            $this->command->info("   ✅ {$perm->name}");
        }

        $this->command->info("🎉 Admin verification completed!");
    }
}
