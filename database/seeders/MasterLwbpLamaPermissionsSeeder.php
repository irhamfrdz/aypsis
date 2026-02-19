<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class MasterLwbpLamaPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Master LWBP Lama permissions
        $permissions = [
            [
                'name' => 'master-lwbp-lama-view',
                'description' => 'Melihat daftar LWBP lama'
            ],
            [
                'name' => 'master-lwbp-lama-create',
                'description' => 'Menambah LWBP lama'
            ],
            [
                'name' => 'master-lwbp-lama-update',
                'description' => 'Mengedit LWBP lama'
            ],
            [
                'name' => 'master-lwbp-lama-delete',
                'description' => 'Menghapus LWBP lama'
            ],
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                ['description' => $permissionData['description']]
            );
        }

        $this->command->info('Master LWBP Lama permissions created successfully!');
    }
}
