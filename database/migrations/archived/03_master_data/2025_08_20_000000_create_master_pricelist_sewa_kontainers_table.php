<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('master_pricelist_sewa_kontainers', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tagihan')->unique();
            $table->string('vendor');
            $table->string('tarif');
            $table->string('ukuran_kontainer');
            $table->decimal('harga', 15, 2);
            $table->date('tanggal_harga_awal');
            $table->date('tanggal_harga_akhir')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('master_pricelist_sewa_kontainers');
    }
};
