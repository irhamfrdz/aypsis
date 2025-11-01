<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\NomorTerakhir;

class AddPembayaranAktivitasLainnyaToNomorTerakhir extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Check if module already exists
        $existing = NomorTerakhir::where('modul', 'pembayaran_aktivitas_lainnya')->first();

        if (!$existing) {
            NomorTerakhir::create([
                'modul' => 'pembayaran_aktivitas_lainnya',
                'nomor_terakhir' => 0,
                'prefix' => 'PAL',
                'keterangan' => 'Nomor pembayaran aktivitas lainnya'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        NomorTerakhir::where('modul', 'pembayaran_aktivitas_lainnya')->delete();
    }
}
