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
            $table->json('gambar_tanda_terima')->nullable()->after('catatan')->comment('Array path gambar tanda terima dalam format JSON');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            $table->dropColumn('gambar_tanda_terima');
        });
    }
};
