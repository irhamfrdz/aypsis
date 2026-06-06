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
        Schema::create('rincian_kontainer_pelindos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tanda_terima_id')->nullable();
            $table->string('nomor_kontainer');
            $table->string('ukuran')->nullable();
            $table->string('no_seal')->nullable();
            $table->string('kegiatan')->nullable();
            $table->string('estimasi_nama_kapal')->nullable();
            $table->date('tanggal')->nullable();
            $table->timestamps();

            $table->foreign('tanda_terima_id')->references('id')->on('tanda_terimas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rincian_kontainer_pelindos');
    }
};
