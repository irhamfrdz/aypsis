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
        Schema::table('pricelist_uang_jalan_batam', function (Blueprint $table) {
            // Tambah kolom tarif_base untuk menyimpan tarif awal/original
            $table->decimal('tarif_base', 15, 2)->nullable()->after('tarif');
        });
        
        // Copy tarif saat ini ke tarif_base untuk data yang sudah ada
        DB::statement('UPDATE pricelist_uang_jalan_batam SET tarif_base = tarif WHERE tarif_base IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricelist_uang_jalan_batam', function (Blueprint $table) {
            $table->dropColumn('tarif_base');
        });
    }
};
