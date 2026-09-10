<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alat_berats', function (Blueprint $table) {
            $table->string('nomor_sertifikat_sia')->nullable()->after('nomor_seri');
            $table->date('tanggal_terbit_sertifikat_sia')->nullable()->after('nomor_sertifikat_sia');
            $table->date('tanggal_kadaluarsa_sertifikat_sia')->nullable()->after('tanggal_terbit_sertifikat_sia');
        });
    }

    public function down(): void
    {
        Schema::table('alat_berats', function (Blueprint $table) {
            $table->dropColumn([
                'nomor_sertifikat_sia',
                'tanggal_terbit_sertifikat_sia',
                'tanggal_kadaluarsa_sertifikat_sia',
            ]);
        });
    }
};
