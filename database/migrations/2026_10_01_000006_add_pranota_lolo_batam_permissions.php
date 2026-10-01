<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            'pranota-lolo-batam-view' => 'Melihat Daftar Pranota LOLO Batam',
            'pranota-lolo-batam-create' => 'Membuat Pranota LOLO Batam',
            'pranota-lolo-batam-update' => 'Mengubah Pranota LOLO Batam',
            'pranota-lolo-batam-delete' => 'Menghapus Pranota LOLO Batam',
            'pranota-lolo-batam-approve' => 'Menyetujui Pranota LOLO Batam',
            'pranota-lolo-batam-print' => 'Mencetak Pranota LOLO Batam',
            'pranota-lolo-batam-export' => 'Mengekspor Pranota LOLO Batam',
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
            'pranota-lolo-batam-view',
            'pranota-lolo-batam-create',
            'pranota-lolo-batam-update',
            'pranota-lolo-batam-delete',
            'pranota-lolo-batam-approve',
            'pranota-lolo-batam-print',
            'pranota-lolo-batam-export',
        ])->delete();
    }
};
