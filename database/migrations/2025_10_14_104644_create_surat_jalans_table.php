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
        Schema::create('surat_jalans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_surat_jalan');
            $table->string('no_surat_jalan')->unique();
            $table->string('pengirim')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telp')->nullable();
            $table->string('jenis_barang')->nullable();
            $table->string('tujuan_pengambilan')->nullable();
            $table->string('retur_barang')->nullable();
            $table->integer('jumlah_retur')->default(0);
            $table->string('karyawan')->nullable();
            $table->string('supir')->nullable();
            $table->string('supir2')->nullable();
            $table->string('no_plat')->nullable();
            $table->string('kenek')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->string('no_kontainer')->nullable();
            $table->string('no_seal')->nullable();
            $table->string('size')->nullable();
            $table->integer('karton')->default(0);
            $table->integer('plastik')->default(0);
            $table->integer('terpal')->default(0);
            $table->timestamp('waktu_berangkat')->nullable();
            $table->string('tujuan_pengiriman')->nullable();
            $table->date('tanggal_muat')->nullable();
            $table->time('jam_berangkat')->nullable();
            $table->string('term')->nullable();
            $table->text('aktifitas')->nullable();
            $table->integer('rit')->default(0);
            $table->decimal('uang_jalan', 15, 2)->default(0);
            $table->string('no_pemesanan')->nullable();
            $table->string('gambar')->nullable();
            $table->unsignedBigInteger('input_by')->nullable();
            $table->timestamp('input_date')->nullable();
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled'])->default('draft');
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('input_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['tanggal_surat_jalan', 'status']);
            $table->index(['no_surat_jalan']);
            $table->index(['pengirim']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_jalans');
    }
};
