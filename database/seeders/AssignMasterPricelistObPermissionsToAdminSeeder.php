<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AssignMasterPricelistObPermissionsToAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionNames = [
            'master-pricelist-ob-view',
            'master-pricelist-ob-create',
            'master-pricelist-ob-update',
            'master-pricelist-ob-delete',
        ];

        $permissionsToAssign = Permission::whereIn('name', $permissionNames)->get();

        if ($permissionsToAssign->isEmpty()) {
            $this->command->error("❌ Master Pricelist OB permissions not found. Please run MasterPricelistObPermissionSeeder first.");
            return;
        }

        // Find admin users (common usernames: admin, administrator, superadmin) or user ID 1
        $adminUsers = User::where('id', 1)
                        ->orWhere('username', 'admin')
                        ->orWhere('username', 'administrator')
                        ->orWhere('username', 'superadmin')
                        ->get();

        if ($adminUsers->isEmpty()) {
            $this->command->warn("⚠️ No admin users found with usernames: admin, administrator, or superadmin, or ID 1.");
            $this->command->line("Please run this seeder after creating admin users or modify the query in this seeder.");
            return;
        }

        DB::transaction(function () use ($adminUsers, $permissionsToAssign) {
            foreach ($adminUsers as $admin) {
                $existingPermissionIds = $admin->permissions()->pluck('permission_id')->toArray();
                $newPermissionIds = $permissionsToAssign->whereNotIn('id', $existingPermissionIds)->pluck('id')->toArray();

                if (!empty($newPermissionIds)) {
                    $admin->permissions()->attach($newPermissionIds);
                    $this->command->info("✅ Assigned " . count($newPermissionIds) . " Master Pricelist OB permissions to user: {$admin->username}");
                } else {
                    $this->command->info("ℹ️  User {$admin->username} already has all Master Pricelist OB permissions");
                }
            }
        });

        $this->command->info("🎉 Admin Master Pricelist OB permission assignment completed!");
    }
}
