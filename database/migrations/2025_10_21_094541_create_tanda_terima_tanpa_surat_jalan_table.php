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
        Schema::create('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            $table->id();
            $table->string('no_tanda_terima')->unique();
            $table->date('tanggal_tanda_terima');
            $table->string('penerima');
            $table->string('pengirim');
            $table->text('alamat_pengirim')->nullable();
            $table->text('alamat_penerima')->nullable();
            $table->string('jenis_barang');
            $table->integer('jumlah_barang');
            $table->string('satuan_barang')->default('unit');
            $table->text('keterangan_barang')->nullable();
            $table->decimal('berat', 8, 2)->nullable();
            $table->string('satuan_berat')->default('kg')->nullable();
            $table->string('tujuan_pengambilan');
            $table->string('tujuan_pengiriman');
            $table->string('supir')->nullable();
            $table->string('no_plat')->nullable();
            $table->enum('status', ['draft', 'terkirim', 'diterima', 'selesai'])->default('draft');
            $table->text('catatan')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index('no_tanda_terima');
            $table->index('tanggal_tanda_terima');
            $table->index('status');
            $table->index('penerima');
            $table->index('pengirim');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanda_terima_tanpa_surat_jalan');
    }
};
