<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class MasterStockKontainerPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Master Stock Kontainer permissions
            [
                'name' => 'master-stock-kontainer',
                'description' => 'Manajemen Stock Kontainer'
            ],
            [
                'name' => 'master-stock-kontainer-view',
                'description' => 'Melihat data stock kontainer'
            ],
            [
                'name' => 'master-stock-kontainer-create',
                'description' => 'Membuat stock kontainer baru'
            ],
            [
                'name' => 'master-stock-kontainer-update',
                'description' => 'Mengupdate data stock kontainer'
            ],
            [
                'name' => 'master-stock-kontainer-delete',
                'description' => 'Menghapus stock kontainer'
            ]
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['description' => $permission['description']]
            );
        }

        $this->command->info('Master Stock Kontainer permissions seeded successfully');
    }
}
