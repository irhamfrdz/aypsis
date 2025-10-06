<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('vendor')->nullable();
            $table->string('nomor_kontainer')->nullable();
            $table->date('tanggal_awal')->nullable();
            $table->date('tanggal_akhir')->nullable();
            $table->string('group')->nullable();
            $table->string('periode')->nullable();
            $table->string('masa')->nullable();
            $table->string('tarif')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            // Indexes for common lookups
            $table->index(['vendor']);
            $table->index(['nomor_kontainer']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daftar_tagihan_kontainer_sewa');
    }
};
