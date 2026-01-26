<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\User;

class KaryawanTidakTetapPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'karyawan-tidak-tetap-view',
            'karyawan-tidak-tetap-create',
            'karyawan-tidak-tetap-update',
            'karyawan-tidak-tetap-delete',
        ];

        $newPermissions = [];
        $existingPermissions = [];

        foreach ($permissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            
            if (!$permission) {
                Permission::create(['name' => $permissionName]);
                $newPermissions[] = $permissionName;
            } else {
                $existingPermissions[] = $permissionName;
            }
        }

        // Auto-assign permissions to admin user (ID 1)
        $adminUser = User::find(1);
        if ($adminUser) {
            foreach ($permissions as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission && !$adminUser->hasPermissionTo($permissionName)) {
                    // Assuming using a pivot table or Spatie package method
                    // Based on MasterDataPermissionsSeeder, it uses $adminUser->permissions()->attach($permission->id);
                    // But standard Spatie usage is assignRole or givePermissionTo.
                    // MasterDataPermissionsSeeder.php line 102 says: $adminUser->permissions()->attach($permission->id);
                    // I will follow that pattern but check if givePermissionTo exists just in case, or stick to attach which is safer if using custom relation.
                    
                    // Actually, let's stick to what MasterDataPermissionsSeeder does.
                    $adminUser->permissions()->attach($permission->id);
                }
            }
        }

        $this->command->info('=== KARYAWAN TIDAK TETAP PERMISSIONS SEEDER RESULTS ===');
        $this->command->info('✅ New permissions created: ' . count($newPermissions));
        foreach ($newPermissions as $perm) {
            $this->command->info("   - {$perm}");
        }

        $this->command->info('ℹ️  Existing permissions found: ' . count($existingPermissions));
        
        if ($adminUser) {
            $this->command->info('✅ All permissions assigned to admin user');
        } else {
            $this->command->warn('⚠️  Admin user not found - permissions not auto-assigned');
        }
    }
}
