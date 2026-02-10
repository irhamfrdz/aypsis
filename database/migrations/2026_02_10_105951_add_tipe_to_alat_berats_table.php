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
        Schema::table('alat_berats', function (Blueprint $table) {
            $table->integer('tahun_pembuatan')->nullable()->after('nomor_seri');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alat_berats', function (Blueprint $table) {
            $table->dropColumn('tahun_pembuatan');
        });
    }
};
