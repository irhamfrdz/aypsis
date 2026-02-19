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
            $table->string('kamvas_rem_depan_kanan')->default('berfungsi');
            $table->string('kamvas_rem_depan_kiri')->default('berfungsi');
            $table->string('kamvas_rem_belakang_kanan')->default('berfungsi');
            $table->string('kamvas_rem_belakang_kiri')->default('berfungsi');
            $table->string('spion_kanan')->default('berfungsi');
            $table->string('spion_kiri')->default('berfungsi');
            $table->string('tekanan_ban_depan_kanan')->default('berfungsi');
            $table->string('tekanan_ban_depan_kiri')->default('berfungsi');
            $table->string('tekanan_ban_belakang_kanan')->default('berfungsi');
            $table->string('tekanan_ban_belakang_kiri')->default('berfungsi');
            $table->string('ganjelan_ban')->default('ada');
            $table->string('trakel_sabuk')->default('ada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cek_kendaraans', function (Blueprint $table) {
            $table->dropColumn([
                'kamvas_rem_depan_kanan',
                'kamvas_rem_depan_kiri',
                'kamvas_rem_belakang_kanan',
                'kamvas_rem_belakang_kiri',
                'spion_kanan',
                'spion_kiri',
                'tekanan_ban_depan_kanan',
                'tekanan_ban_depan_kiri',
                'tekanan_ban_belakang_kanan',
                'tekanan_ban_belakang_kiri',
                'ganjelan_ban',
                'trakel_sabuk',
            ]);
        });
    }
};
