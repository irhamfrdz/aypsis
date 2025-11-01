<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Membuat user admin dan memberikan semua permissions
     */
    public function run(): void
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->command->info("🔧 Creating admin user with all permissions...");

        // Create or update admin user
        $adminUser = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'status' => 'active',
                'role_id' => null, // We'll use permissions directly
                'registration_reason' => 'System Administrator Account',
                'approved_by' => null,
                'approved_at' => now(),
                'karyawan_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info("✅ Admin user created/updated: {$adminUser->username}");
        $this->command->info("   - Status: {$adminUser->status}");
        $this->command->info("   - Password: admin123");

        // Get all permissions
        $allPermissions = Permission::all();
        $this->command->info("📋 Found {$allPermissions->count()} total permissions");

        // Clear existing permissions for admin (if any)
        DB::table('user_permissions')->where('user_id', $adminUser->id)->delete();

        // Assign all permissions to admin user
        $permissionData = [];
        foreach ($allPermissions as $permission) {
            $permissionData[] = [
                'user_id' => $adminUser->id,
                'permission_id' => $permission->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert all permissions in batch
        DB::table('user_permissions')->insert($permissionData);

        $this->command->info("🎯 Assigned {$allPermissions->count()} permissions to admin user");

        // Show some key permissions assigned
        $keyPermissions = [
            'master-user-view',
            'master-user-create',
            'master-user-update', 
            'master-user-delete',
            'pranota-uang-kenek-view',
            'pranota-uang-kenek-create',
            'pranota-uang-kenek-update',
            'pranota-uang-kenek-delete',
            'pranota-uang-kenek-approve',
            'pranota-uang-kenek-mark-paid'
        ];

        $this->command->info("🔑 Key permissions assigned include:");
        foreach ($keyPermissions as $permName) {
            $permission = $allPermissions->where('name', $permName)->first();
            if ($permission) {
                $this->command->info("   ✅ {$permName}");
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info("🎉 Admin user setup completed successfully!");
        $this->command->info("📝 Login credentials:");
        $this->command->info("   - Username: admin");
        $this->command->info("   - Password: admin123");
        $this->command->info("   - Total Permissions: {$allPermissions->count()}");
    }
}
