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
            $columns = [
                'oli_mesin',
                'air_radiator',
                'minyak_rem',
                'air_wiper',
                'lampu_depan',
                'lampu_belakang',
                'lampu_sein',
                'lampu_rem',
                'kondisi_ban',
                'tekanan_ban',
                'aki',
                'fungsi_rem',
                'fungsi_kopling',
                'kebersihan_interior',
                'kebersihan_eksterior',
                'bahan_bakar',
                'kotak_p3k',
                'plat_no_belakang',
                'lampu_besar_dekat_kanan',
                'lampu_besar_dekat_kiri',
                'lampu_rem_kanan',
                'lampu_rem_kiri',
                'lampu_alarm_mundur',
                'kamvas_rem_depan_kanan',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('cek_kendaraans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cek_kendaraans', function (Blueprint $table) {
            //
        });
    }
};
