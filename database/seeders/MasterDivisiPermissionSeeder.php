<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class MasterDivisiPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Master Divisi permissions
            [
                'name' => 'master-divisi',
                'description' => 'Manajemen Divisi'
            ],
            [
                'name' => 'master-divisi.view',
                'description' => 'Melihat data divisi'
            ],
            [
                'name' => 'master-divisi.create',
                'description' => 'Membuat divisi baru'
            ],
            [
                'name' => 'master-divisi.update',
                'description' => 'Mengupdate data divisi'
            ],
            [
                'name' => 'master-divisi.delete',
                'description' => 'Menghapus divisi'
            ],
            [
                'name' => 'master-divisi.print',
                'description' => 'Mencetak data divisi'
            ],
            [
                'name' => 'master-divisi.export',
                'description' => 'Mengekspor data divisi'
            ]
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['description' => $permission['description']]
            );
        }

        $this->command->info('Master Divisi permissions seeded successfully');
    }
}
