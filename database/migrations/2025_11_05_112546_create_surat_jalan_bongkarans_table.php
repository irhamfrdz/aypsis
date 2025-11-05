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
        Schema::create('surat_jalan_bongkarans', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->unsignedBigInteger('order_id')->nullable();
            $table->date('tanggal_surat_jalan');
            $table->string('no_surat_jalan')->unique();
            $table->enum('kegiatan', ['bongkar_muat', 'delivery', 'pickup', 'stuffing', 'stripping', 'lainnya'])->nullable();
            
            // Sender Information
            $table->string('pengirim')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telp')->nullable();
            
            // Cargo Information
            $table->string('jenis_barang')->nullable();
            $table->string('tujuan_pengambilan')->nullable();
            $table->string('tujuan_pengiriman')->nullable();
            
            // Return Information
            $table->string('retur_barang')->nullable();
            $table->integer('jumlah_retur')->default(0);
            
            // Personnel Information
            $table->string('karyawan')->nullable();
            $table->string('supir')->nullable();
            $table->string('supir2')->nullable();
            $table->string('no_plat')->nullable();
            $table->string('kenek')->nullable();
            
            // Container Information
            $table->string('tipe_kontainer')->nullable();
            $table->string('no_kontainer')->nullable();
            $table->string('no_seal')->nullable();
            $table->string('size')->nullable();
            $table->integer('jumlah_kontainer')->default(1);
            
            // Packaging Information
            $table->string('karton')->default('tidak');
            $table->string('plastik')->default('tidak');
            $table->string('terpal')->default('tidak');
            
            // Schedule Information
            $table->datetime('waktu_berangkat')->nullable();
            $table->date('tanggal_muat')->nullable();
            $table->time('jam_berangkat')->nullable();
            
            // Order Information
            $table->string('term')->nullable();
            $table->enum('rit', ['menggunakan_rit', 'tidak_menggunakan_rit'])->nullable();
            $table->decimal('uang_jalan', 15, 2)->default(0);
            $table->string('no_pemesanan')->nullable();
            
            // Financial Information
            $table->enum('status_pembayaran', ['belum_bayar', 'lunas', 'sebagian'])->nullable();
            $table->enum('status_pembayaran_uang_rit', ['belum_bayar', 'lunas'])->nullable();
            $table->enum('status_pembayaran_uang_rit_kenek', ['belum_bayar', 'lunas'])->nullable();
            $table->decimal('total_tarif', 15, 2)->default(0);
            $table->decimal('jumlah_terbayar', 15, 2)->default(0);
            
            // Media Information
            $table->string('gambar')->nullable();
            $table->string('gambar_checkpoint')->nullable();
            
            // System Information
            $table->string('input_by')->nullable();
            $table->datetime('input_date')->nullable();
            
            // Status and Activities
            $table->enum('status', [
                'draft', 
                'active', 
                'completed', 
                'cancelled',
                'belum masuk checkpoint',
                'sudah masuk checkpoint',
                'gate in',
                'gate out'
            ])->default('draft');
            $table->text('aktifitas')->nullable();
            
            // Timestamps
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('set null');

            // Indexes for performance
            $table->index(['tanggal_surat_jalan', 'status']);
            $table->index(['no_surat_jalan']);
            $table->index(['pengirim']);
            $table->index(['status']);
            $table->index(['order_id']);
            $table->index(['no_kontainer']);
            $table->index(['kegiatan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_jalan_bongkarans');
    }
};
