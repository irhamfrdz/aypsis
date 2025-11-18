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
        Schema::table('realisasi_uang_muka', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['kegiatan']);
            // Then drop the column
            $table->dropColumn('kegiatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('realisasi_uang_muka', function (Blueprint $table) {
            $table->unsignedBigInteger('kegiatan')->nullable()->after('id');
            $table->foreign('kegiatan')->references('id')->on('master_kegiatans')->onDelete('cascade');
        });
    }
};
