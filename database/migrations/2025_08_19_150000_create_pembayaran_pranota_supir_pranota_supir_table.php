<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pembayaran_pranota_supir_pranota_supir', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_pranota_supir_id');
            $table->unsignedBigInteger('pranota_supir_id');
            $table->timestamps();

            $table->foreign('pembayaran_pranota_supir_id', 'fk_pembayaran_id')
                ->references('id')->on('pembayaran_pranota_supir')->onDelete('cascade');
            $table->foreign('pranota_supir_id', 'fk_pranota_id')
                ->references('id')->on('pranota_supirs')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayaran_pranota_supir_pranota_supir');
    }
};
