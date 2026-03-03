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
        Schema::create('master_dokumen_kapal_alexindos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kapal_id')->constrained('master_kapals')->onDelete('cascade');
            $table->string('nama_dokumen');
            $table->string('nomor_dokumen')->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->date('tanggal_berakhir')->nullable();
            $table->string('file_dokumen')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_dokumen_kapal_alexindos');
    }
};
