<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserAdminSeeder extends Seeder
{
    /**
     * Run the database seeds to create user_admin with all permissions.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting user_admin seeding...');

        // Check if permissions exist
        $allPermissions = Permission::all();
        if ($allPermissions->isEmpty()) {
            $this->command->error('❌ No permissions found! Please run ComprehensivePermissionsSeeder first.');
            return;
        }

        $this->command->info('📊 Found ' . $allPermissions->count() . ' permissions in system');

        // Create user_admin with all permissions
        $this->command->info('👤 Creating user_admin...');

        $userAdmin = User::firstOrCreate(
            ['username' => 'user_admin'],
            [
                'karyawan_id' => null, // Can be assigned to a specific karyawan if needed
                'password' => Hash::make('admin123'),
                'status' => 'approved', // Changed from 'active' to 'approved' to match middleware requirement
                'registration_reason' => 'Auto-generated super admin user with all permissions',
                'approved_by' => null, // Self-approved
                'approved_at' => now(),
            ]
        );

        // Assign ALL permissions to user_admin
        $userAdmin->permissions()->sync($allPermissions->pluck('id'));

        $this->command->info('✅ user_admin created/updated successfully!');
        $this->command->info('🔑 Login credentials:');
        $this->command->info('   Username: user_admin');
        $this->command->info('   Password: admin123');
        $this->command->info('   Status: approved (auto-approved)');
        $this->command->info('🔐 Permissions assigned: ' . $allPermissions->count() . ' (ALL permissions)');

        // Verify the assignment
        $assignedPermissionsCount = $userAdmin->permissions()->count();
        if ($assignedPermissionsCount === $allPermissions->count()) {
            $this->command->info('✅ Verification successful: All permissions assigned correctly');
        } else {
            $this->command->warn('⚠️  Warning: Expected ' . $allPermissions->count() . ' permissions, but assigned ' . $assignedPermissionsCount);
        }

        $this->command->info('🎉 user_admin seeding completed successfully!');
        $this->command->info('💡 Note: You can change the default password after first login for security.');
    }
}
