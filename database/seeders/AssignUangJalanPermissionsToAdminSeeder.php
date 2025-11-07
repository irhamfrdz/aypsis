<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AssignUangJalanPermissionsToAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all uang jalan related permissions
        $uangJalanPermissions = Permission::whereIn('name', [
            // Uang Jalan permissions
            'uang-jalan-view',
            'uang-jalan-create', 
            'uang-jalan-update',
            'uang-jalan-delete',
            'uang-jalan-approve',
            'uang-jalan-print',
            'uang-jalan-export',
            
            // Pranota Uang Jalan permissions
            'pranota-uang-jalan-view',
            'pranota-uang-jalan-create',
            'pranota-uang-jalan-update', 
            'pranota-uang-jalan-delete',
            'pranota-uang-jalan-approve',
            'pranota-uang-jalan-print',
            'pranota-uang-jalan-export'
        ])->get();

        if ($uangJalanPermissions->isEmpty()) {
            $this->command->error("❌ No uang jalan permissions found! Please run UangJalanPermissionSeeder first.");
            return;
        }

        // Find admin users (you can adjust this query based on your admin identification logic)
        $adminUsers = User::where('username', 'admin')
                         ->orWhere('username', 'administrator')
                         ->orWhere('username', 'superadmin')
                         ->get();

        if ($adminUsers->isEmpty()) {
            $this->command->warn("⚠️  No admin users found with usernames: admin, administrator, or superadmin");
            $this->command->line("   Please run this seeder again after creating admin users or modify the query in the seeder.");
            return;
        }

        DB::transaction(function () use ($adminUsers, $uangJalanPermissions) {
            foreach ($adminUsers as $admin) {
                // Get permission IDs that the admin doesn't already have
                $existingPermissionIds = $admin->permissions()->pluck('permission_id')->toArray();
                $newPermissionIds = $uangJalanPermissions->whereNotIn('id', $existingPermissionIds)->pluck('id')->toArray();

                if (!empty($newPermissionIds)) {
                    // Assign new permissions to admin without removing existing ones
                    $admin->permissions()->attach($newPermissionIds);
                    
                    $this->command->info("✅ Assigned " . count($newPermissionIds) . " uang jalan permissions to user: {$admin->username}");
                } else {
                    $this->command->info("ℹ️  User {$admin->username} already has all uang jalan permissions");
                }
            }
        });

        $this->command->info("🎉 Admin uang jalan permissions assignment completed!");
        $this->command->line("");
        $this->command->line("📋 Summary:");
        $this->command->line("   • Users processed: " . $adminUsers->count());
        $this->command->line("   • Permissions available: " . $uangJalanPermissions->count());
        $this->command->line("");
        $this->command->line("🔧 Next steps:");
        $this->command->line("   1. Verify permissions in User Management interface");
        $this->command->line("   2. Test access to Uang Jalan and Pranota Uang Jalan modules");
        $this->command->line("   3. Assign permissions to other users as needed");
    }
}