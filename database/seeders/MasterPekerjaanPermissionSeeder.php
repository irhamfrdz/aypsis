<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class MasterPekerjaanPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Master Pekerjaan permissions
            [
                'name' => 'master-pekerjaan',
                'description' => 'Manajemen Pekerjaan'
            ],
            [
                'name' => 'master-pekerjaan.view',
                'description' => 'Melihat data pekerjaan'
            ],
            [
                'name' => 'master-pekerjaan.create',
                'description' => 'Membuat pekerjaan baru'
            ],
            [
                'name' => 'master-pekerjaan.update',
                'description' => 'Mengupdate data pekerjaan'
            ],
            [
                'name' => 'master-pekerjaan.delete',
                'description' => 'Menghapus pekerjaan'
            ],
            [
                'name' => 'master-pekerjaan.print',
                'description' => 'Mencetak data pekerjaan'
            ],
            [
                'name' => 'master-pekerjaan.export',
                'description' => 'Mengekspor data pekerjaan'
            ]
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['description' => $permission['description']]
            );
        }

        $this->command->info('Master Pekerjaan permissions seeded successfully');
    }
}
