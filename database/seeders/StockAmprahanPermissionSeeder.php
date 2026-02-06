<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class StockAmprahanPermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'name' => 'stock-amprahan-view',
                'description' => 'Melihat daftar dan detail stock amprahan',
            ],
            [
                'name' => 'stock-amprahan-create',
                'description' => 'Membuat data stock amprahan baru',
            ],
            [
                'name' => 'stock-amprahan-update',
                'description' => 'Mengubah data stock amprahan',
            ],
            [
                'name' => 'stock-amprahan-delete',
                'description' => 'Menghapus data stock amprahan',
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
            $this->command->info('Stock Amprahan permissions seeded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error seeding permissions: ' . $e->getMessage());
        }
    }
}
