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
        Schema::table('pricelist_cat', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_harga_awal',
                'tanggal_harga_akhir',
                'harga',
                'keterangan'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricelist_cat', function (Blueprint $table) {
            $table->date('tanggal_harga_awal')->nullable();
            $table->date('tanggal_harga_akhir')->nullable();
            $table->decimal('harga', 15, 2)->nullable();
            $table->text('keterangan')->nullable();
        });
    }
};
