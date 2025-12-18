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
        Schema::table('invoice_aktivitas_lain', function (Blueprint $table) {
            $table->unsignedBigInteger('surat_jalan_id')->nullable()->after('nomor_voyage');
            $table->string('jenis_penyesuaian')->nullable()->after('surat_jalan_id');
            $table->json('tipe_penyesuaian')->nullable()->after('jenis_penyesuaian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_aktivitas_lain', function (Blueprint $table) {
            $table->dropColumn(['surat_jalan_id', 'jenis_penyesuaian', 'tipe_penyesuaian']);
        });
    }
};
