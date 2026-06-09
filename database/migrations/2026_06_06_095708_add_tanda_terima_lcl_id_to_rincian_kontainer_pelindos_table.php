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
            $table->unsignedBigInteger('tanda_terima_lcl_id')->nullable()->after('tanda_terima_tanpa_surat_jalan_id');

            $table->foreign('tanda_terima_lcl_id', 'fk_rincian_kontainer_tt_lcl')
                ->references('id')
                ->on('tanda_terimas_lcl')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rincian_kontainer_pelindos', function (Blueprint $table) {
            $table->dropForeign('fk_rincian_kontainer_tt_lcl');
            $table->dropColumn('tanda_terima_lcl_id');
        });
    }
};
