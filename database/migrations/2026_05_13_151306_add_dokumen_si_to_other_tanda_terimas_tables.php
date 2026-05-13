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
        Schema::table('tanda_terimas_lcl', function (Blueprint $table) {
            $table->text('dokumen_si')->nullable()->after('dokumen_faktur_pajak');
        });

        Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            $table->text('dokumen_si')->nullable()->after('dokumen_faktur_pajak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terimas_lcl', function (Blueprint $table) {
            $table->dropColumn('dokumen_si');
        });

        Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            $table->dropColumn('dokumen_si');
        });
    }
};
