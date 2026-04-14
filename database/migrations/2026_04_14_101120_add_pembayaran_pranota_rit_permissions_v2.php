<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            ['name' => 'pembayaran-pranota-rit-view', 'description' => 'Melihat daftar pembayaran pranota rit'],
            ['name' => 'pembayaran-pranota-rit-create', 'description' => 'Membuat pembayaran pranota rit'],
            ['name' => 'pembayaran-pranota-rit-edit', 'description' => 'Mengubah data pembayaran pranota rit'],
            ['name' => 'pembayaran-pranota-rit-delete', 'description' => 'Menghapus pembayaran pranota rit'],
            ['name' => 'pembayaran-pranota-rit-approve', 'description' => 'Menyetujui pembayaran pranota rit'],
            ['name' => 'pembayaran-pranota-rit-print', 'description' => 'Mencetak bukti pembayaran pranota rit'],
            ['name' => 'pembayaran-pranota-rit-export', 'description' => 'Ekspor data pembayaran pranota rit'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission['name']],
                [
                    'description' => $permission['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')->where('name', 'like', 'pembayaran-pranota-rit-%')->delete();
    }
};
