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
        Schema::table('cek_kendaraans', function (Blueprint $table) {
            $table->string('kotak_p3k')->default('tidak_kadaluarsa');
            $table->string('racun_api')->default('ada');
            $table->string('plat_no_depan')->default('ada');
            $table->string('plat_no_belakang')->default('ada');
            $table->string('lampu_jauh_kanan')->default('berfungsi');
            $table->string('lampu_jauh_kiri')->default('berfungsi');
            $table->string('lampu_dekat_kanan')->default('berfungsi');
            $table->string('lampu_dekat_kiri')->default('berfungsi');
            $table->string('lampu_sein_depan_kanan')->default('berfungsi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cek_kendaraans', function (Blueprint $table) {
            $table->dropColumn([
                'kotak_p3k',
                'racun_api',
                'plat_no_depan',
                'plat_no_belakang',
                'lampu_jauh_kanan',
                'lampu_jauh_kiri',
                'lampu_dekat_kanan',
                'lampu_dekat_kiri',
                'lampu_sein_depan_kanan',
            ]);
        });
    }
};
