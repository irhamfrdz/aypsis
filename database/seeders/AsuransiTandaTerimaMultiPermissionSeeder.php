<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class AsuransiTandaTerimaMultiPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'asuransi-tanda-terima-multi-view',
            'asuransi-tanda-terima-multi-create',
            'asuransi-tanda-terima-multi-update',
            'asuransi-tanda-terima-multi-delete',
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission], [
                'description' => ucwords(str_replace('-', ' ', $permission))
            ]);
        }

        $this->command->info('✅ Permissions for multi asuransi created.');
    }
}
