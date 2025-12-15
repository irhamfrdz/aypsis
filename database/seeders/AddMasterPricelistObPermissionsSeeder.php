<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddMasterPricelistObPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'master-pricelist-ob-view',
            'master-pricelist-ob-create',
            'master-pricelist-ob-update',
            'master-pricelist-ob-delete',
        ];

        foreach ($permissions as $name) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        $this->command->info('Master pricelist OB permissions seeded/updated.');
    }
}
