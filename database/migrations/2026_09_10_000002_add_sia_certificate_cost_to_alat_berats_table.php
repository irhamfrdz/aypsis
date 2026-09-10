<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alat_berats', function (Blueprint $table) {
            $table->decimal('biaya_sertifikat_sia', 15, 2)
                ->nullable()
                ->after('tanggal_kadaluarsa_sertifikat_sia');
        });
    }

    public function down(): void
    {
        Schema::table('alat_berats', function (Blueprint $table) {
            $table->dropColumn('biaya_sertifikat_sia');
        });
    }
};
