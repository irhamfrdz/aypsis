<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class VerifyDashboardPermissionSeeder extends Seeder
{
    /**
     * Verify dashboard permissions
     */
    public function run(): void
    {
        $this->command->info("🔍 Verifying dashboard permissions...");

        // Get all dashboard permissions
        $dashboardPermissions = Permission::where('name', 'like', '%dashboard%')->get();
        
        $this->command->info("📊 All Dashboard Permissions ({$dashboardPermissions->count()}):");
        foreach ($dashboardPermissions as $perm) {
            $this->command->info("   ✅ {$perm->name}: {$perm->description}");
        }

        // Check admin user permissions
        $adminUser = DB::table('users')->where('username', 'admin')->first();
        if ($adminUser) {
            $adminDashboardPermissions = DB::table('user_permissions')
                ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
                ->where('user_permissions.user_id', $adminUser->id)
                ->where('permissions.name', 'like', '%dashboard%')
                ->get(['permissions.name', 'permissions.description']);

            $this->command->info("🔑 Admin User Dashboard Permissions ({$adminDashboardPermissions->count()}):");
            foreach ($adminDashboardPermissions as $perm) {
                $this->command->info("   ✅ {$perm->name}: {$perm->description}");
            }

            // Check total permissions for admin
            $totalAdminPermissions = DB::table('user_permissions')
                ->where('user_id', $adminUser->id)
                ->count();
            
            $totalPermissions = Permission::count();
            
            $this->command->info("📈 Admin Permission Summary:");
            $this->command->info("   - Total permissions in system: {$totalPermissions}");
            $this->command->info("   - Admin has permissions: {$totalAdminPermissions}");
            
            if ($totalAdminPermissions == $totalPermissions) {
                $this->command->info("   ✅ Admin has ALL permissions (including new dashboard permissions)");
            } else {
                $this->command->warn("   ⚠️  Admin missing some permissions!");
            }
        }

        $this->command->info("🎉 Dashboard permission verification completed!");
    }
}
