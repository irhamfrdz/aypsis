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
        Schema::create('prospek_batams', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->nullable();
            $table->string('nama_supir')->nullable();
            $table->string('supir_ob')->nullable();
            $table->string('barang')->nullable();
            $table->string('pt_pengirim')->nullable();
            $table->string('penerima')->nullable();
            $table->string('ukuran')->nullable();
            $table->string('tipe')->nullable();
            $table->string('no_surat_jalan')->nullable();
            $table->unsignedBigInteger('surat_jalan_batam_id')->nullable();
            $table->unsignedBigInteger('tanda_terima_batam_id')->nullable();
            $table->string('nomor_kontainer')->nullable();
            $table->string('no_seal')->nullable();
            $table->string('tujuan_pengiriman')->nullable();
            $table->decimal('total_ton', 15, 3)->nullable();
            $table->integer('kuantitas')->nullable();
            $table->decimal('total_volume', 15, 3)->nullable();
            $table->string('nama_kapal')->nullable();
            $table->unsignedBigInteger('kapal_id')->nullable();
            $table->string('no_voyage')->nullable();
            $table->string('pelabuhan_asal')->nullable();
            $table->date('tanggal_muat')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('status')->default('aktif');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('surat_jalan_batam_id');
            $table->index('tanda_terima_batam_id');
            $table->index('no_surat_jalan');
            $table->index('status');
            $table->index('nomor_kontainer');
            $table->index('no_seal');

            // Foreign Keys
            $table->foreign('surat_jalan_batam_id')->references('id')->on('surat_jalan_batams')->onDelete('set null');
            $table->foreign('tanda_terima_batam_id')->references('id')->on('tanda_terima_batams')->onDelete('set null');
            $table->foreign('kapal_id')->references('id')->on('master_kapals')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospek_batams');
    }
};
