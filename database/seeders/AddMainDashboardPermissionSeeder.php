<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AddMainDashboardPermissionSeeder extends Seeder
{
    /**
     * Add main dashboard permission that DashboardController needs
     */
    public function run(): void
    {
        $this->command->info("🎛️ Adding main dashboard permission...");

        // Create main dashboard permission
        $dashboardPermission = Permission::firstOrCreate(
            ['name' => 'dashboard'],
            ['description' => 'Access Main Dashboard']
        );

        if ($dashboardPermission->wasRecentlyCreated) {
            $this->command->info("✅ Created 'dashboard' permission");
        } else {
            $this->command->info("ℹ️ 'dashboard' permission already exists");
        }

        // Get all users who have any dashboard-view permission (they should also have main dashboard access)
        $usersWithDashboardView = User::whereHas('permissions', function($query) {
            $query->where('name', 'dashboard-view');
        })->get();

        $this->command->info("👥 Adding dashboard permission to users with dashboard-view...");
        
        foreach ($usersWithDashboardView as $user) {
            if (!$user->permissions()->where('permission_id', $dashboardPermission->id)->exists()) {
                $user->permissions()->attach($dashboardPermission->id);
                $this->command->info("   ✅ Added to: {$user->username}");
            } else {
                $this->command->info("   ℹ️ {$user->username} already has dashboard permission");
            }
        }

        // Also add to admin users (users with many permissions)
        $adminUsers = User::whereHas('permissions', function($query) {
            $query->whereIn('name', ['admin-debug-perms', 'master-user-index']);
        })->get();

        $this->command->info("👑 Adding dashboard permission to admin users...");
        
        foreach ($adminUsers as $user) {
            if (!$user->permissions()->where('permission_id', $dashboardPermission->id)->exists()) {
                $user->permissions()->attach($dashboardPermission->id);
                $this->command->info("   ✅ Added to admin: {$user->username}");
            } else {
                $this->command->info("   ℹ️ Admin {$user->username} already has dashboard permission");
            }
        }

        $this->command->info("🎉 Main dashboard permission setup completed!");
        
        // Show summary
        $totalUsersWithDashboard = User::whereHas('permissions', function($query) {
            $query->where('name', 'dashboard');
        })->count();
        
        $this->command->info("📊 Total users with dashboard permission: {$totalUsersWithDashboard}");
    }
}