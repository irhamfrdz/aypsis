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
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            // Hapus kolom tarif_nominal jika ada
            if (Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'tarif_nominal')) {
                $table->dropColumn('tarif_nominal');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            // Kembalikan kolom tarif_nominal
            $table->decimal('tarif_nominal', 15, 2)->nullable()->after('masa_sewa');
        });
    }
};
