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
        Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            $table->string('surat_jalan_pabrik')->nullable()->after('nomor_surat_jalan_customer');
            $table->date('tanggal_surat_jalan_pabrik')->nullable()->after('surat_jalan_pabrik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            $table->dropColumn(['surat_jalan_pabrik', 'tanggal_surat_jalan_pabrik']);
        });
    }
};
