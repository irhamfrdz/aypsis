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
        Schema::table('biaya_kapals', function (Blueprint $table) {
            $table->decimal('pph_dokumen', 15, 2)->nullable()->after('keterangan')->comment('PPH 2% untuk Biaya Dokumen');
            $table->decimal('grand_total_dokumen', 15, 2)->nullable()->after('pph_dokumen')->comment('Grand Total untuk Biaya Dokumen (Nominal - PPH)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapals', function (Blueprint $table) {
            $table->dropColumn(['pph_dokumen', 'grand_total_dokumen']);
        });
    }
};
