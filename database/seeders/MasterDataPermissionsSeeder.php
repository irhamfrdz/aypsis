<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\User;

class MasterDataPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Master data permissions array
        $masterDataPermissions = [
            // COA Permissions
            'master-coa-view',
            'master-coa-create',
            'master-coa-update',
            'master-coa-delete',

            // Pekerjaan Permissions
            'master-pekerjaan-view',
            'master-pekerjaan-create',
            'master-pekerjaan-update',
            'master-pekerjaan-delete',

            // Vendor Bengkel Permissions
            'master-vendor-bengkel-view',
            'master-vendor-bengkel-create',
            'master-vendor-bengkel-update',
            'master-vendor-bengkel-delete',

            // Kode Nomor Permissions
            'master-kode-nomor-view',
            'master-kode-nomor-create',
            'master-kode-nomor-update',
            'master-kode-nomor-delete',

            // Stock Kontainer Permissions
            'master-stock-kontainer-view',
            'master-stock-kontainer-create',
            'master-stock-kontainer-update',
            'master-stock-kontainer-delete',

            // Tipe Akun Permissions
            'master-tipe-akun-view',
            'master-tipe-akun-create',
            'master-tipe-akun-update',
            'master-tipe-akun-delete',

            // Nomor Terakhir Permissions
            'master-nomor-terakhir-view',
            'master-nomor-terakhir-create',
            'master-nomor-terakhir-update',
            'master-nomor-terakhir-delete',

            // Karyawan Additional Permissions
            'master-karyawan-template',
            'master-karyawan-crew-checklist',

            // Profile Permissions
            'profile-view',
            'profile-update',
            'profile-delete',

            // Admin Features
            'admin-features',
            'admin-debug',
            'admin-user-approval',

            // Pranota Permissions
            'pranota-view',
            'pranota-create',
            'pranota-update',
            'pranota-delete',
            'pranota-kontainer-sewa-view',
        ];

        $newPermissions = [];
        $existingPermissions = [];

        foreach ($masterDataPermissions as $permission) {
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
                $newPermissions[] = $permission;
            } else {
                $existingPermissions[] = $permission;
            }
        }

        // Auto-assign all these permissions to admin user (ID 1)
        $adminUser = User::find(1);
        if ($adminUser) {
            foreach ($masterDataPermissions as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission && !$adminUser->hasPermissionTo($permissionName)) {
                    // Attach permission menggunakan pivot table
                    $adminUser->permissions()->attach($permission->id);
                }
            }
        }

        $this->command->info('=== MASTER DATA PERMISSIONS SEEDER RESULTS ===');
        $this->command->info('✅ New permissions created: ' . count($newPermissions));
        foreach ($newPermissions as $perm) {
            $this->command->info("   - {$perm}");
        }

        $this->command->info('ℹ️  Existing permissions found: ' . count($existingPermissions));
        foreach ($existingPermissions as $perm) {
            $this->command->info("   - {$perm}");
        }

        if ($adminUser) {
            $this->command->info('✅ All permissions assigned to admin user');
        } else {
            $this->command->warn('⚠️  Admin user not found - permissions not auto-assigned');
        }
    }
}
