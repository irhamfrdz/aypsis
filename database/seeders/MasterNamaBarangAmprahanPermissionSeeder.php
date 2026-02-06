<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class MasterNamaBarangAmprahanPermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'name' => 'master-nama-barang-amprahan-view',
                'description' => 'Melihat daftar master nama barang amprahan',
            ],
            [
                'name' => 'master-nama-barang-amprahan-create',
                'description' => 'Menambah master nama barang amprahan',
            ],
            [
                'name' => 'master-nama-barang-amprahan-update',
                'description' => 'Mengubah master nama barang amprahan',
            ],
            [
                'name' => 'master-nama-barang-amprahan-delete',
                'description' => 'Menghapus master nama barang amprahan',
            ],
            [
                'name' => 'master-nama-barang-amprahan-import',
                'description' => 'Import master nama barang amprahan dari Excel',
            ],
            [
                'name' => 'master-nama-barang-amprahan-export',
                'description' => 'Export master nama barang amprahan ke Excel',
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
            $this->command->info('Master Nama Barang Amprahan permissions seeded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error seeding permissions: ' . $e->getMessage());
        }
    }
}
