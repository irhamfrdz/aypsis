<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class InvoiceTagihanVendorPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                'name' => 'invoice-tagihan-vendor-view',
                'description' => 'Melihat daftar dan detail invoice tagihan vendor',
            ],
            [
                'name' => 'invoice-tagihan-vendor-create',
                'description' => 'Membuat invoice tagihan vendor baru',
            ],
            [
                'name' => 'invoice-tagihan-vendor-update',
                'description' => 'Mengupdate status pembayaran invoice tagihan vendor',
            ],
            [
                'name' => 'invoice-tagihan-vendor-delete',
                'description' => 'Menghapus invoice tagihan vendor',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                ['description' => $permission['description']]
            );
        }
    }
}
