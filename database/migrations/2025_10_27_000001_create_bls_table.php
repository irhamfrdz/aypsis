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
        Schema::create('bls', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kontainer')->nullable()->index();
            $table->string('no_seal')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->string('no_voyage')->nullable()->index();
            $table->string('nama_kapal')->nullable()->index();
            $table->string('nama_barang')->nullable();
            $table->decimal('tonnage', 12, 3)->nullable();
            $table->integer('kuantitas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bls');
    }
};
