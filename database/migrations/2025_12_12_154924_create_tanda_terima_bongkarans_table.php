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
        Schema::create('tanda_terima_bongkarans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tanda_terima')->unique();
            $table->date('tanggal_tanda_terima');
            $table->foreignId('surat_jalan_bongkaran_id')->constrained('surat_jalan_bongkarans')->onDelete('cascade');
            $table->string('no_kontainer')->nullable();
            $table->string('no_seal')->nullable();
            $table->enum('kegiatan', ['muat', 'bongkar', 'stuffing', 'stripping']);
            $table->enum('status', ['pending', 'approved', 'completed'])->default('pending');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanda_terima_bongkarans');
    }
};
