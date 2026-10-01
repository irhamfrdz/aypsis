<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            'master-pricelist-lolo-batam-view' => 'Melihat Master Pricelist LOLO Batam',
            'master-pricelist-lolo-batam-create' => 'Membuat Master Pricelist LOLO Batam',
            'master-pricelist-lolo-batam-update' => 'Mengubah Master Pricelist LOLO Batam',
            'master-pricelist-lolo-batam-delete' => 'Menghapus Master Pricelist LOLO Batam',
            'master-pricelist-lolo-batam-export' => 'Mengekspor Master Pricelist LOLO Batam',
        ];

        foreach ($permissions as $name => $description) {
            DB::table('permissions')->insertOrIgnore([
                'name' => $name,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('permissions')->whereIn('name', [
            'master-pricelist-lolo-batam-view',
            'master-pricelist-lolo-batam-create',
            'master-pricelist-lolo-batam-update',
            'master-pricelist-lolo-batam-delete',
            'master-pricelist-lolo-batam-export',
        ])->delete();
    }
};
