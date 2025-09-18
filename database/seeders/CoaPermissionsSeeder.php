<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class CoaPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'master-coa-index',
                'description' => 'Melihat daftar COA (Chart of Accounts)',
            ],
            [
                'name' => 'master-coa-create',
                'description' => 'Membuat COA baru',
            ],
            [
                'name' => 'master-coa-update',
                'description' => 'Mengedit COA yang sudah ada',
            ],
            [
                'name' => 'master-coa-destroy',
                'description' => 'Menghapus COA',
            ],
            [
                'name' => 'master-coa-show',
                'description' => 'Melihat detail COA',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
