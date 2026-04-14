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
            ['name' => 'pembayaran-pranota-stock-view', 'description' => 'Melihat daftar pembayaran pranota stock', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pembayaran-pranota-stock-create', 'description' => 'Membuat pembayaran pranota stock', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pembayaran-pranota-stock-edit', 'description' => 'Mengubah data pembayaran pranota stock', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'pembayaran-pranota-stock-delete', 'description' => 'Menghapus pembayaran pranota stock', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('permissions')->insert($permissions);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('permissions')->whereIn('name', [
            'pembayaran-pranota-stock-view',
            'pembayaran-pranota-stock-create',
            'pembayaran-pranota-stock-edit',
            'pembayaran-pranota-stock-delete',
        ])->delete();
    }
};
