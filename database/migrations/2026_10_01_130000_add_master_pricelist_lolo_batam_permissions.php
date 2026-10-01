<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const PERMISSIONS = [
        'master-pricelist-lolo-batam-view' => 'Melihat Pricelist LOLO Batam',
        'master-pricelist-lolo-batam-create' => 'Menambah Pricelist LOLO Batam',
        'master-pricelist-lolo-batam-update' => 'Mengubah Pricelist LOLO Batam',
        'master-pricelist-lolo-batam-delete' => 'Menghapus Pricelist LOLO Batam',
    ];

    public function up(): void
    {
        foreach (self::PERMISSIONS as $name => $description) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $name],
                [
                    'description' => $description,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        // Permission ini juga dimiliki migrasi pembuat modul, sehingga tidak
        // dihapus saat rollback agar akses modul yang sudah ada tetap terjaga.
    }
};
