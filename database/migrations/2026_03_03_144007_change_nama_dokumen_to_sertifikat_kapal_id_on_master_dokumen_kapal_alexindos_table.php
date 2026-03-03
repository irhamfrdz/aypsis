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
        Schema::table('master_dokumen_kapal_alexindos', function (Blueprint $table) {
            $table->dropColumn('nama_dokumen');
            $table->foreignId('sertifikat_kapal_id')->nullable()->after('kapal_id')->constrained('sertifikat_kapals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_dokumen_kapal_alexindos', function (Blueprint $table) {
            $table->dropForeign(['sertifikat_kapal_id']);
            $table->dropColumn('sertifikat_kapal_id');
            $table->string('nama_dokumen')->after('kapal_id');
        });
    }
};
