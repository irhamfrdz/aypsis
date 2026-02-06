<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds to create roles, permissions, and default users.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting roles and permissions assignment...');

        // 1. Create Roles
        $this->command->info('📝 Creating roles...');

        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['description' => 'Administrator Sistem - Akses Penuh']
        );

        $managerRole = Role::firstOrCreate(
            ['name' => 'manager'],
            ['description' => 'Manager - Akses Terbatas untuk Operasional']
        );

        $staffRole = Role::firstOrCreate(
            ['name' => 'staff'],
            ['description' => 'Staff Operasional - Akses Dasar']
        );

        $supervisorRole = Role::firstOrCreate(
            ['name' => 'supervisor'],
            ['description' => 'Supervisor - Akses untuk Supervisi']
        );

        $supirRole = Role::firstOrCreate(
            ['name' => 'supir'],
            ['description' => 'Supir - Akses Terbatas untuk Supir']
        );

        $this->command->info('✅ Roles created successfully!');

        // 2. Get all permissions
        $allPermissions = Permission::all();
        $this->command->info('📊 Found ' . $allPermissions->count() . ' permissions in system');

        // 3. Assign permissions to roles
        $this->command->info('🔗 Assigning permissions to roles...');

        // Admin gets ALL permissions
        $adminRole->permissions()->sync($allPermissions->pluck('id'));
        $this->command->info('✅ Admin role assigned all permissions');

        // Manager permissions (most permissions except system admin functions)
        $managerPermissions = $allPermissions->filter(function ($permission) {
            $excludedPatterns = [
                'master-user', // User management
                'master-permission', // Permission management
                'user-approval', // User approval system
            ];

            foreach ($excludedPatterns as $pattern) {
                if (strpos($permission->name, $pattern) !== false) {
                    return false;
                }
            }
            return true;
        });
        $managerRole->permissions()->sync($managerPermissions->pluck('id'));
        $this->command->info('✅ Manager role assigned ' . $managerPermissions->count() . ' permissions');

        // Supervisor permissions (operational + approval permissions)
        $supervisorPermissions = $allPermissions->filter(function ($permission) {
            $allowedPatterns = [
                'dashboard',
                'master-karyawan',
                'master-kontainer',
                'master-tujuan',
                'master-kegiatan',
                'master-mobil',
                'master-pricelist-sewa-kontainer',
                'master-cabang',
                'master-divisi',
                'master-pekerjaan',
                'master-pajak',
                'master-bank',
                'master-coa',
                'pranota',
                'pembayaran',
                'tagihan',
                'permohonan',
                'perbaikan-kontainer',
                'approval',
                'print',
                'export',
                'master-gudang-amprahan'
            ];

            foreach ($allowedPatterns as $pattern) {
                if (strpos($permission->name, $pattern) !== false) {
                    return true;
                }
            }
            return false;
        });
        $supervisorRole->permissions()->sync($supervisorPermissions->pluck('id'));
        $this->command->info('✅ Supervisor role assigned ' . $supervisorPermissions->count() . ' permissions');

        // Staff permissions (basic operational permissions)
        $staffPermissions = $allPermissions->filter(function ($permission) {
            $allowedPatterns = [
                'dashboard',
                'master-karyawan.view',
                'master-kontainer.view',
                'master-tujuan.view',
                'master-kegiatan.view',
                'master-mobil.view',
                'master-pricelist-sewa-kontainer.view',
                'master-cabang.view',
                'master-divisi.view',
                'master-pekerjaan.view',
                'master-pajak.view',
                'master-bank.view',
                'master-coa.view',
                'pranota.view',
                'pembayaran.view',
                'tagihan.view',
                'permohonan.view',
                'perbaikan-kontainer.view',
                'print',
                'export',
                'master-gudang-amprahan.view'
            ];

            foreach ($allowedPatterns as $pattern) {
                if (strpos($permission->name, $pattern) !== false) {
                    return true;
                }
            }
            return false;
        });
        $staffRole->permissions()->sync($staffPermissions->pluck('id'));
        $this->command->info('✅ Staff role assigned ' . $staffPermissions->count() . ' permissions');

        // Supir permissions (very limited - only their own pranota)
        $supirPermissions = $allPermissions->filter(function ($permission) {
            $allowedPatterns = [
                'dashboard',
                'pranota-supir.view',
                'pranota-supir.update',
                'pembayaran-pranota-supir.view'
            ];

            foreach ($allowedPatterns as $pattern) {
                if (strpos($permission->name, $pattern) !== false) {
                    return true;
                }
            }
            return false;
        });
        $supirRole->permissions()->sync($supirPermissions->pluck('id'));
        $this->command->info('✅ Supir role assigned ' . $supirPermissions->count() . ' permissions');

        // 4. Create default users
        $this->command->info('👤 Creating default users...');

        // Admin User
        $adminUser = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator Sistem',
                'karyawan_id' => 1,
                'password' => Hash::make('admin123'),
                'email' => 'admin@aypsis.com',
                'status' => 'active'
            ]
        );
        $adminUser->roles()->sync([$adminRole->id]);
        $adminUser->permissions()->sync($allPermissions->pluck('id'));
        $this->command->info('✅ Admin user created/updated');

        // Manager User
        $managerUser = User::firstOrCreate(
            ['username' => 'manager'],
            [
                'name' => 'Manager Operasional',
                'karyawan_id' => 2,
                'password' => Hash::make('manager123'),
                'email' => 'manager@aypsis.com',
                'status' => 'active'
            ]
        );
        $managerUser->roles()->sync([$managerRole->id]);
        $managerUser->permissions()->sync($managerPermissions->pluck('id'));
        $this->command->info('✅ Manager user created/updated');

        // Staff User
        $staffUser = User::firstOrCreate(
            ['username' => 'staff'],
            [
                'name' => 'Staff Operasional',
                'karyawan_id' => 3,
                'password' => Hash::make('staff123'),
                'email' => 'staff@aypsis.com',
                'status' => 'active'
            ]
        );
        $staffUser->roles()->sync([$staffRole->id]);
        $staffUser->permissions()->sync($staffPermissions->pluck('id'));
        $this->command->info('✅ Staff user created/updated');

        // Supervisor User
        $supervisorUser = User::firstOrCreate(
            ['username' => 'supervisor'],
            [
                'name' => 'Supervisor',
                'karyawan_id' => 4,
                'password' => Hash::make('supervisor123'),
                'email' => 'supervisor@aypsis.com',
                'status' => 'active'
            ]
        );
        $supervisorUser->roles()->sync([$supervisorRole->id]);
        $supervisorUser->permissions()->sync($supervisorPermissions->pluck('id'));
        $this->command->info('✅ Supervisor user created/updated');

        // Supir User
        $supirUser = User::firstOrCreate(
            ['username' => 'supir'],
            [
                'name' => 'Supir Truck',
                'karyawan_id' => 5,
                'password' => Hash::make('supir123'),
                'email' => 'supir@aypsis.com',
                'status' => 'active'
            ]
        );
        $supirUser->roles()->sync([$supirRole->id]);
        $supirUser->permissions()->sync($supirPermissions->pluck('id'));
        $this->command->info('✅ Supir user created/updated');

        $this->command->info('🎉 Roles and permissions assignment completed successfully!');
        $this->command->info('📊 Summary:');
        $this->command->info('   - Roles created: 5');
        $this->command->info('   - Users created: 5');
        $this->command->info('   - Total permissions: ' . $allPermissions->count());

        // Display role-permission summary
        $this->command->info('📋 Role-Permission Summary:');
        $this->command->info('   - Admin: ' . $allPermissions->count() . ' permissions (full access)');
        $this->command->info('   - Manager: ' . $managerPermissions->count() . ' permissions');
        $this->command->info('   - Supervisor: ' . $supervisorPermissions->count() . ' permissions');
        $this->command->info('   - Staff: ' . $staffPermissions->count() . ' permissions');
        $this->command->info('   - Supir: ' . $supirPermissions->count() . ' permissions');
    }
}
