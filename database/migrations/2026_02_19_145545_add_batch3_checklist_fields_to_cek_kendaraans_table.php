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
            $table->string('twist_lock_kontainer')->default('berfungsi');
            $table->string('landing_buntut')->default('berfungsi');
            $table->string('patok_besi')->default('ada');
            $table->string('tutup_tangki')->default('ada');
            $table->string('lampu_no_plat')->default('berfungsi');
            $table->string('lampu_bahaya')->default('berfungsi');
            $table->string('klakson')->default('berfungsi');
            $table->string('radio')->default('berfungsi');
            $table->string('rem_tangan')->default('berfungsi');
            $table->string('pedal_gas')->default('berfungsi');
            $table->string('pedal_rem')->default('berfungsi');
            $table->string('porseneling')->default('berfungsi');
            $table->string('antena_radio')->default('ada');
            $table->string('speaker')->default('berfungsi');
            $table->string('spion_dalam')->default('berfungsi');
            $table->string('dongkrak')->default('ada');
            $table->string('tangkai_dongkrak')->default('ada');
            $table->string('kunci_roda')->default('ada');
            $table->string('dop_roda')->default('ada');
            $table->string('wiper_depan')->default('berfungsi');
            $table->string('oli_mesin')->default('baik');
            $table->string('air_radiator')->default('baik');
            $table->string('minyak_rem')->default('baik');
            $table->string('air_wiper')->default('baik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cek_kendaraans', function (Blueprint $table) {
            $table->dropColumn([
                'twist_lock_kontainer',
                'landing_buntut',
                'patok_besi',
                'tutup_tangki',
                'lampu_no_plat',
                'lampu_bahaya',
                'klakson',
                'radio',
                'rem_tangan',
                'pedal_gas',
                'pedal_rem',
                'porseneling',
                'antena_radio',
                'speaker',
                'spion_dalam',
                'dongkrak',
                'tangkai_dongkrak',
                'kunci_roda',
                'dop_roda',
                'wiper_depan',
                'oli_mesin',
                'air_radiator',
                'minyak_rem',
                'air_wiper',
            ]);
        });
    }
};
