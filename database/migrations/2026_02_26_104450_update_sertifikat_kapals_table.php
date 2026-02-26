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
        Schema::table('sertifikat_kapals', function (Blueprint $table) {
            $table->dropColumn('keterangan');
            $table->string('name_certificate')->nullable();
            $table->string('nickname')->nullable();
            $table->string('jenis_dokumen')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sertifikat_kapals', function (Blueprint $table) {
            $table->text('keterangan')->nullable();
            $table->dropColumn(['name_certificate', 'nickname', 'jenis_dokumen']);
        });
    }
};
