<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class MasterGudangAmprahanPermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'name' => 'master-gudang-amprahan-view',
                'description' => 'Melihat daftar master gudang amprahan',
            ],
            [
                'name' => 'master-gudang-amprahan-create',
                'description' => 'Menambah master gudang amprahan',
            ],
            [
                'name' => 'master-gudang-amprahan-update',
                'description' => 'Mengubah master gudang amprahan',
            ],
            [
                'name' => 'master-gudang-amprahan-delete',
                'description' => 'Menghapus master gudang amprahan',
            ],
        ];

        DB::beginTransaction();

        try {
            foreach ($permissions as $perm) {
                // Create or update permission
                $permission = Permission::firstOrCreate(
                    ['name' => $perm['name']],
                    ['description' => $perm['description']]
                );
                
                // Find admin role
                $adminRole = Role::where('name', 'admin')->first();
                
                if ($adminRole) {
                    // Attach permission to role if not already attached
                    if (!$adminRole->permissions()->where('permissions.id', $permission->id)->exists()) {
                        $adminRole->permissions()->attach($permission->id);
                        $this->command->info("Attached {$perm['name']} to admin role.");
                    } else {
                        $this->command->info("Permission {$perm['name']} already attached to admin role.");
                    }
                }
            }
            
            DB::commit();
            $this->command->info('Master Gudang Amprahan permissions seeded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error seeding permissions: ' . $e->getMessage());
        }
    }
}
