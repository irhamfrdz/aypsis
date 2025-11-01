<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class CheckDashboardPermissionSeeder extends Seeder
{
    /**
     * Check and add missing dashboard permissions
     */
    public function run(): void
    {
        $this->command->info("🔍 Checking dashboard permissions...");

        // Check current dashboard permissions
        $dashboardPermissions = Permission::where('name', 'like', '%dashboard%')->get();
        
        $this->command->info("📋 Current dashboard permissions:");
        foreach ($dashboardPermissions as $perm) {
            $this->command->info("   ✅ {$perm->name}: {$perm->description}");
        }

        // Define missing dashboard permissions that should exist
        $requiredDashboardPermissions = [
            'dashboard-view' => 'Access Main Dashboard',
            'dashboard-admin' => 'Access Admin Dashboard',
            'dashboard-operational' => 'Access Operational Dashboard',
            'dashboard-financial' => 'Access Financial Dashboard',
            'dashboard-reports' => 'Access Dashboard Reports',
            'dashboard-analytics' => 'Access Dashboard Analytics',
            'dashboard-widgets' => 'Manage Dashboard Widgets',
            'dashboard-export' => 'Export Dashboard Data',
            'dashboard-print' => 'Print Dashboard Reports'
        ];

        $missingPermissions = [];
        foreach ($requiredDashboardPermissions as $permName => $description) {
            $exists = Permission::where('name', $permName)->exists();
            if (!$exists) {
                $missingPermissions[] = [
                    'name' => $permName,
                    'description' => $description
                ];
            }
        }

        if (empty($missingPermissions)) {
            $this->command->info("✅ All dashboard permissions already exist!");
            return;
        }

        $this->command->info("🚨 Missing dashboard permissions found:");
        foreach ($missingPermissions as $perm) {
            $this->command->warn("   ❌ {$perm['name']}: {$perm['description']}");
        }

        // Add missing permissions
        $this->command->info("🔧 Adding missing dashboard permissions...");
        
        foreach ($missingPermissions as $perm) {
            Permission::create([
                'name' => $perm['name'],
                'description' => $perm['description'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $this->command->info("   ✅ Added: {$perm['name']}");
        }

        // Assign new permissions to admin user
        $adminUser = DB::table('users')->where('username', 'admin')->first();
        if ($adminUser) {
            foreach ($missingPermissions as $perm) {
                $permission = Permission::where('name', $perm['name'])->first();
                if ($permission) {
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
                        $this->command->info("   🔗 Assigned {$perm['name']} to admin user");
                    }
                }
            }
        }

        $this->command->info("🎉 Dashboard permissions update completed!");
    }
}
