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
        Schema::table('uang_jalans', function (Blueprint $table) {
            $table->unsignedBigInteger('surat_jalan_bongkaran_id')->nullable()->after('surat_jalan_id');
            $table->foreign('surat_jalan_bongkaran_id')->references('id')->on('surat_jalan_bongkarans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uang_jalans', function (Blueprint $table) {
            $table->dropForeign(['surat_jalan_bongkaran_id']);
            $table->dropColumn('surat_jalan_bongkaran_id');
        });
    }
};
