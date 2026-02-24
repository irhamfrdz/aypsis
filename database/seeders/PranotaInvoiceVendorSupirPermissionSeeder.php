<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PranotaInvoiceVendorSupirPermissionSeeder extends Seeder
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
                'name' => 'pranota-invoice-vendor-supir-view',
                'description' => 'Melihat daftar dan detail pranota invoice vendor supir',
            ],
            [
                'name' => 'pranota-invoice-vendor-supir-create',
                'description' => 'Membuat pranota invoice vendor supir baru',
            ],
            [
                'name' => 'pranota-invoice-vendor-supir-update',
                'description' => 'Mengupdate status pembayaran pranota invoice vendor supir',
            ],
            [
                'name' => 'pranota-invoice-vendor-supir-delete',
                'description' => 'Menghapus pranota invoice vendor supir',
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
