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
        Schema::table('tujuan_kegiatan_utamas', function (Blueprint $table) {
            $table->string('kode')->nullable()->after('nama');
            $table->string('cabang')->nullable()->after('kode');
            $table->string('wilayah')->nullable()->after('cabang');
            $table->string('dari')->nullable()->after('wilayah');
            $table->string('ke')->nullable()->after('dari');
            $table->decimal('uang_jalan_20ft', 15, 2)->nullable()->after('ke');
            $table->decimal('uang_jalan_40ft', 15, 2)->nullable()->after('uang_jalan_20ft');
            $table->text('keterangan')->nullable()->after('uang_jalan_40ft');
            $table->decimal('liter', 10, 2)->nullable()->after('keterangan');
            $table->decimal('jarak_dari_penjaringan_km', 10, 2)->nullable()->after('liter');
            $table->decimal('mel_20ft', 15, 2)->nullable()->after('jarak_dari_penjaringan_km');
            $table->decimal('mel_40ft', 15, 2)->nullable()->after('mel_20ft');
            $table->decimal('ongkos_truk_20ft', 15, 2)->nullable()->after('mel_40ft');
            $table->decimal('ongkos_truk_40ft', 15, 2)->nullable()->after('ongkos_truk_20ft');
            $table->decimal('antar_lokasi_20ft', 15, 2)->nullable()->after('ongkos_truk_40ft');
            $table->decimal('antar_lokasi_40ft', 15, 2)->nullable()->after('antar_lokasi_20ft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tujuan_kegiatan_utamas', function (Blueprint $table) {
            $table->dropColumn([
                'kode',
                'cabang',
                'wilayah',
                'dari',
                'ke',
                'uang_jalan_20ft',
                'uang_jalan_40ft',
                'keterangan',
                'liter',
                'jarak_dari_penjaringan_km',
                'mel_20ft',
                'mel_40ft',
                'ongkos_truk_20ft',
                'ongkos_truk_40ft',
                'antar_lokasi_20ft',
                'antar_lokasi_40ft'
            ]);
        });
    }
};
