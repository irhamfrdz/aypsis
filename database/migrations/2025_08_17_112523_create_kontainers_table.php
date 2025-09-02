<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kontainers', function (Blueprint $table) {
            $table->id();
            $table->string('awalan_kontainer', 4);
            $table->string('nomor_seri_kontainer', 6);
            $table->string('akhiran_kontainer', 1);
            $table->string('nomor_seri_gabungan', 11)->unique();
            $table->string('ukuran');
            $table->string('tipe_kontainer');
            $table->date('tanggal_beli')->nullable();
            $table->date('tanggal_jual')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('kondisi_kontainer')->nullable();
            $table->date('tanggal_kondisi_terakhir')->nullable();
            $table->date('tanggal_masuk_sewa')->nullable();
            $table->date('tanggal_selesai_sewa')->nullable();
            $table->string('pemilik_kontainer')->nullable();
            $table->string('tahun_pembuatan', 4)->nullable();
            $table->string('kontainer_asal')->nullable();
            $table->text('keterangan1')->nullable();
            $table->text('keterangan2')->nullable();
            $table->string('status')->nullable()->default('Tersedia');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kontainers');
    }
};
