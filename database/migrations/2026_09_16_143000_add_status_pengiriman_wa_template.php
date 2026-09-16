<?php

use App\Models\WaTemplate;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        WaTemplate::firstOrCreate(
            ['nama_template' => 'STATUS PENGIRIMAN'],
            [
                'isi_template' => "Halo Bapak/Ibu *{shipper_name}*,\n\nBerikut kami sampaikan update *Status Pengiriman* muatan Anda pada kapal *{nama_kapal}* (Voy. *{no_voyage}*):\n\n*Status:* {kategori_masalah}\n*Keterangan:* {deskripsi_masalah}\n\n*Daftar Resi / Kontainer:*\n{daftar_resi}\n\nApabila ada pertanyaan lebih lanjut, silakan hubungi tim operasional kami.\nTerima kasih,\n*PT. ALEXINDO YAKINPRIMA*",
                'is_active' => true,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        WaTemplate::where('nama_template', 'STATUS PENGIRIMAN')->delete();
    }
};
