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
            $table->string('kondisi_aki')->default('baik');
            $table->string('pengukur_tekanan_ban')->default('ada');
            $table->string('segitiga_pengaman')->default('ada');
            $table->string('jumlah_ban_serep')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cek_kendaraans', function (Blueprint $table) {
            $table->dropColumn([
                'kondisi_aki',
                'pengukur_tekanan_ban',
                'segitiga_pengaman',
                'jumlah_ban_serep',
            ]);
        });
    }
};
