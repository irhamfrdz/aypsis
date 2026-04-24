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
        Schema::table('surat_jalan_bongkaran_batams', function (Blueprint $table) {
            $table->renameColumn('no_surat_jalan', 'nomor_surat_jalan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalan_bongkaran_batams', function (Blueprint $table) {
            $table->renameColumn('nomor_surat_jalan', 'no_surat_jalan');
        });
    }
};
