<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            'tagihan-lolo-batam-view' => 'Melihat Daftar Tagihan LOLO Batam',
            'tagihan-lolo-batam-create' => 'Membuat Tagihan LOLO Batam',
            'tagihan-lolo-batam-update' => 'Mengubah Tagihan LOLO Batam',
            'tagihan-lolo-batam-delete' => 'Menghapus Tagihan LOLO Batam',
            'tagihan-lolo-batam-approve' => 'Menyetujui Tagihan LOLO Batam',
            'tagihan-lolo-batam-print' => 'Mencetak Tagihan LOLO Batam',
            'tagihan-lolo-batam-export' => 'Mengekspor Tagihan LOLO Batam',
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
            'tagihan-lolo-batam-view',
            'tagihan-lolo-batam-create',
            'tagihan-lolo-batam-update',
            'tagihan-lolo-batam-delete',
            'tagihan-lolo-batam-approve',
            'tagihan-lolo-batam-print',
            'tagihan-lolo-batam-export',
        ])->delete();
    }
};
