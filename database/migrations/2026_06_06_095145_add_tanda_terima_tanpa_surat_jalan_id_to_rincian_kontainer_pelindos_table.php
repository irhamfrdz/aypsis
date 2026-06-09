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
        Schema::table('rincian_kontainer_pelindos', function (Blueprint $table) {
            $table->unsignedBigInteger('tanda_terima_tanpa_surat_jalan_id')->nullable()->after('tanda_terima_id');

            $table->foreign('tanda_terima_tanpa_surat_jalan_id', 'fk_rincian_kontainer_tt_tanpa_sj')
                ->references('id')
                ->on('tanda_terima_tanpa_surat_jalan')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rincian_kontainer_pelindos', function (Blueprint $table) {
            $table->dropForeign('fk_rincian_kontainer_tt_tanpa_sj');
            $table->dropColumn('tanda_terima_tanpa_surat_jalan_id');
        });
    }
};
