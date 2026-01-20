<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class StockBanPermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'name' => 'stock-ban-view',
                'description' => 'Melihat daftar dan detail stock ban',
            ],
            [
                'name' => 'stock-ban-create',
                'description' => 'Membuat data stock ban baru',
            ],
            [
                'name' => 'stock-ban-update',
                'description' => 'Mengubah data stock ban',
            ],
            [
                'name' => 'stock-ban-delete',
                'description' => 'Menghapus data stock ban',
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
                
                // Find admin role (assuming default name is 'admin')
                $adminRole = Role::where('name', 'admin')->first();
                
                if ($adminRole) {
                    // Attach permission to role if not already attached
                    if (!$adminRole->permissions()->where('permissions.id', $permission->id)->exists()) {
                        $adminRole->permissions()->attach($permission->id);
                        $this->command->info("Attached {$perm['name']} to admin role.");
                    } else {
                        $this->command->info("Permission {$perm['name']} already attached to admin role.");
                    }
                } else {
                    $this->command->warn("Role 'admin' not found. Permissions created but not assigned to any role.");
                }
            }
            
            DB::commit();
            $this->command->info('Stock Ban permissions seeded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('Error seeding permissions: ' . $e->getMessage());
        }
    }
}
