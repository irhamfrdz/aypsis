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
        Schema::table('biaya_kapal_barang', function (Blueprint $table) {
            // Ubah kolom jumlah dari integer menjadi decimal(10, 2)
            $table->decimal('jumlah', 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_barang', function (Blueprint $table) {
            // Kembalikan ke integer jika rollback
            $table->integer('jumlah')->change();
        });
    }
};
