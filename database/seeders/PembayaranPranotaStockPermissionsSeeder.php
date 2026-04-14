<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PembayaranPranotaStockPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'pembayaran-pranota-stock-view',
            'pembayaran-pranota-stock-create',
            'pembayaran-pranota-stock-edit',
            'pembayaran-pranota-stock-delete',
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['name' => $permission], ['name' => $permission]);
        }
    }
}
