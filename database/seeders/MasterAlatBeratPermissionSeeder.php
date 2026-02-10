<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class MasterAlatBeratPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $permissions = [
            'master-alat-berat-create',
            'master-alat-berat-delete',
            'master-alat-berat-update',
            'master-alat-berat-view',
        ];

        $created = 0;
        $existing = 0;

        foreach ($permissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();

            if (!$permission) {
                Permission::create([
                    'name' => $permissionName,
                    'description' => ucwords(str_replace('-', ' ', $permissionName)),
                ]);
                
                $this->command->info("✅ Created permission: {$permissionName}");
                $created++;
            } else {
                $this->command->comment("⚠️  Permission already exists: {$permissionName}");
                $existing++;
            }
        }

        // Assign to Admin role
        $adminRole = Role::where('name', 'admin')->first();
        
        if ($adminRole) {
            $permissionIds = Permission::whereIn('name', $permissions)->pluck('id');
            // Check if attached already, but safe to attach/sync without detach? No, Admin should have ALL permissions.
            // Let's attach new ones only to avoid resetting others if we used sync.
            // But usually Admin gets everything via sync in RoleSeeder.
            // Here we just want to ADD the new ones.
            $adminRole->permissions()->syncWithoutDetaching($permissionIds);
            
            $this->command->info("✅ Assigned " . count($permissionIds) . " permissions to Admin role");
        } else {
            $this->command->error("❌ Admin role not found!");
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info("🎉 Master Alat Berat Permission seeding completed!");
        $this->command->info("   - New permissions: {$created}");
        $this->command->info("   - Existing permissions: {$existing}");
    }
}
