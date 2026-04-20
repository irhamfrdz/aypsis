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
            ['name' => 'pembayaran-pranota-rit-kenek-view', 'description' => 'Melihat daftar pembayaran pranota rit kenek'],
            ['name' => 'pembayaran-pranota-rit-kenek-create', 'description' => 'Membuat pembayaran pranota rit kenek'],
            ['name' => 'pembayaran-pranota-rit-kenek-edit', 'description' => 'Mengubah data pembayaran pranota rit kenek'],
            ['name' => 'pembayaran-pranota-rit-kenek-delete', 'description' => 'Menghapus pembayaran pranota rit kenek'],
            ['name' => 'pembayaran-pranota-rit-kenek-approve', 'description' => 'Menyetujui pembayaran pranota rit kenek'],
            ['name' => 'pembayaran-pranota-rit-kenek-print', 'description' => 'Mencetak bukti pembayaran pranota rit kenek'],
            ['name' => 'pembayaran-pranota-rit-kenek-export', 'description' => 'Ekspor data pembayaran pranota rit kenek'],
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
        DB::table('permissions')->where('name', 'like', 'pembayaran-pranota-rit-kenek-%')->delete();
    }
};
