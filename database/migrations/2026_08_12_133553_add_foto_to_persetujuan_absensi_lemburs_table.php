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
        Schema::table('persetujuan_absensi_lemburs', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('keterangan');
            $table->text('detail_lokasi')->nullable()->after('foto');
            $table->string('latitude')->nullable()->after('detail_lokasi');
            $table->string('longitude')->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('persetujuan_absensi_lemburs', function (Blueprint $table) {
            $table->dropColumn(['foto', 'detail_lokasi', 'latitude', 'longitude']);
        });
    }
};
