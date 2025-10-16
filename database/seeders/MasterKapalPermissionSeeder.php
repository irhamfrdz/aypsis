<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class MasterKapalPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define permissions for Master Kapal
        $permissions = [
            [
                'name' => 'master-kapal.view',
                'description' => 'Melihat data master kapal'
            ],
            [
                'name' => 'master-kapal.create',
                'description' => 'Membuat data master kapal baru'
            ],
            [
                'name' => 'master-kapal.edit',
                'description' => 'Mengedit data master kapal'
            ],
            [
                'name' => 'master-kapal.delete',
                'description' => 'Menghapus data master kapal'
            ],
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['description' => $permission['description']]
            );
            $this->command->info("Permission created: {$permission['name']}");
        }

        // Assign all permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $permissionNames = array_column($permissions, 'name');
            $adminRole->givePermissionTo($permissionNames);
            $this->command->info('All Master Kapal permissions assigned to admin role.');
        } else {
            $this->command->warn('Admin role not found. Permissions created but not assigned.');
        }

        $this->command->info('Master Kapal permissions seeded successfully');
    }
}
