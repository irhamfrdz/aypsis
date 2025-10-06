<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('pembayaran_pranota_tagihan_kontainer')) {
            Schema::create('pembayaran_pranota_tagihan_kontainer', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_pembayaran')->unique();
                $table->string('nomor_cetakan')->nullable();
                $table->string('bank')->nullable();
                $table->string('jenis_transaksi')->nullable();
                $table->date('tanggal_kas')->nullable();
                $table->decimal('total_pembayaran', 15, 2)->default(0);
                $table->decimal('penyesuaian', 15, 2)->default(0);
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('pembayaran_pranota_tagihan_kontainer_tagihan')) {
            Schema::create('pembayaran_pranota_tagihan_kontainer_tagihan', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('pembayaran_id');
                $table->unsignedBigInteger('tagihan_id');
                $table->decimal('amount', 15, 2)->default(0);
                $table->timestamps();

                // add short-named foreign keys when creating the table
                $table->foreign('pembayaran_id', 'pp_tk_pemb_fk')->references('id')->on('pembayaran_pranota_tagihan_kontainer')->onDelete('cascade');
                $table->foreign('tagihan_id', 'pp_tk_tag_fk')->references('id')->on('tagihan_kontainer_sewa')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('pembayaran_pranota_tagihan_kontainer_tagihan');
        Schema::dropIfExists('pembayaran_pranota_tagihan_kontainer');
    }
};
