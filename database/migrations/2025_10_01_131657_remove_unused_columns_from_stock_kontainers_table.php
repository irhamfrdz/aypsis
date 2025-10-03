<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_kontainers', function (Blueprint $table) {
            // Hapus kolom yang tidak diperlukan sesuai dengan perubahan form
            $table->dropColumn([
                'kondisi',
                'harga_sewa_per_hari',
                'harga_sewa_per_bulan',
                'pemilik'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_kontainers', function (Blueprint $table) {
            // Kembalikan kolom yang dihapus jika rollback
            $table->string('kondisi')->default('baik')->after('keterangan');
            $table->decimal('harga_sewa_per_hari', 15, 2)->nullable()->after('kondisi');
            $table->decimal('harga_sewa_per_bulan', 15, 2)->nullable()->after('harga_sewa_per_hari');
            $table->string('pemilik')->nullable()->after('harga_sewa_per_bulan');
        });
    }
};
