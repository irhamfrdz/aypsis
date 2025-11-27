<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AssignPranotaUangJalanBongkaranPermissionsToAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permissions to assign
        $permissionsToAssign = Permission::whereIn('name', [
            'pranota-uang-jalan-bongkaran-view',
            'pranota-uang-jalan-bongkaran-create',
            'pranota-uang-jalan-bongkaran-update',
            'pranota-uang-jalan-bongkaran-delete',
            'pranota-uang-jalan-bongkaran-approve',
            'pranota-uang-jalan-bongkaran-print',
            'pranota-uang-jalan-bongkaran-export'
        ])->get();

        if ($permissionsToAssign->isEmpty()) {
            $this->command->error("❌ Pranota Uang Jalan Bongkaran permissions not found. Please run PranotaUangJalanBongkaranPermissionSeeder first.");
            return;
        }

        // Find admin users
        $adminUsers = User::where('username', 'admin')
                         ->orWhere('username', 'administrator')
                         ->orWhere('username', 'superadmin')
                         ->get();

        if ($adminUsers->isEmpty()) {
            $this->command->warn("⚠️ No admin users found with usernames: admin, administrator, or superadmin");
            $this->command->line("   Please run this seeder again after creating admin users or modify the query in this seeder.");
            return;
        }

        DB::transaction(function () use ($adminUsers, $permissionsToAssign) {
            foreach ($adminUsers as $admin) {
                $existingPermissionIds = $admin->permissions()->pluck('permission_id')->toArray();
                $newPermissionIds = $permissionsToAssign->whereNotIn('id', $existingPermissionIds)->pluck('id')->toArray();

                if (!empty($newPermissionIds)) {
                    $admin->permissions()->attach($newPermissionIds);
                    $this->command->info("✅ Assigned " . count($newPermissionIds) . " pranota uang jalan bongkaran permissions to user: {$admin->username}");
                } else {
                    $this->command->info("ℹ️  User {$admin->username} already has all pranota uang jalan bongkaran permissions");
                }
            }
        });

        $this->command->info("🎉 Admin pranota uang jalan bongkaran permission assignment completed!");
    }
}
