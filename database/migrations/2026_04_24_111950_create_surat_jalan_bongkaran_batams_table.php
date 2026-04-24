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
        Schema::create('surat_jalan_bongkaran_batams', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->date('tanggal_surat_jalan');
            $table->string('no_surat_jalan')->unique();
            $table->string('nomor_sj_sebelumnya')->nullable();
            $table->enum('kegiatan', ['bongkar_muat', 'delivery', 'pickup', 'stuffing', 'stripping', 'lainnya'])->nullable();
            
            // Sender/Recipient Information
            $table->string('pengirim')->nullable();
            $table->string('penerima')->nullable();
            $table->text('alamat')->nullable();
            $table->text('tujuan_alamat')->nullable();
            $table->string('telp')->nullable();
            
            // Cargo Information
            $table->text('jenis_barang')->nullable();
            $table->string('tujuan_pengambilan')->nullable();
            $table->string('tujuan_pengiriman')->nullable();
            $table->string('jenis_pengiriman')->nullable();
            
            // Return Information
            $table->string('retur_barang')->nullable();
            $table->integer('jumlah_retur')->default(0);
            
            // Personnel Information
            $table->string('karyawan')->nullable();
            $table->string('supir')->nullable();
            $table->string('supir2')->nullable();
            $table->string('no_plat')->nullable();
            $table->string('kenek')->nullable();
            $table->string('krani')->nullable();
            
            // Container Information
            $table->string('tipe_kontainer')->nullable();
            $table->string('no_kontainer')->nullable();
            $table->string('no_seal')->nullable();
            $table->string('size')->nullable();
            $table->integer('jumlah_kontainer')->default(1);
            $table->string('f_e')->nullable();
            
            // Packaging Information
            $table->string('karton')->default('tidak');
            $table->string('plastik')->default('tidak');
            $table->string('terpal')->default('tidak');
            
            // Schedule Information
            $table->datetime('waktu_berangkat')->nullable();
            $table->date('tanggal_muat')->nullable();
            $table->time('jam_berangkat')->nullable();
            $table->date('tanggal_ambil_barang')->nullable();
            
            // Order & Financial Information
            $table->string('term')->nullable();
            $table->string('rit')->nullable();
            $table->decimal('uang_jalan', 15, 2)->default(0);
            $table->string('uang_jalan_type')->nullable();
            $table->decimal('uang_jalan_nominal', 15, 2)->default(0);
            $table->string('no_pemesanan')->nullable();
            $table->string('tagihan_ayp')->nullable();
            $table->string('tagihan_atb')->nullable();
            $table->string('tagihan_pb')->nullable();
            
            // Financial Status
            $table->string('status_pembayaran')->nullable();
            $table->string('status_pembayaran_uang_jalan')->nullable();
            $table->string('status_pembayaran_uang_rit')->nullable();
            $table->string('status_pembayaran_uang_rit_kenek')->nullable();
            $table->decimal('total_tarif', 15, 2)->default(0);
            $table->decimal('jumlah_terbayar', 15, 2)->default(0);
            
            // Media Information
            $table->string('gambar')->nullable();
            $table->string('gambar_checkpoint')->nullable();
            
            // Vessel/BL Information
            $table->string('nama_kapal')->nullable();
            $table->unsignedBigInteger('kapal_id')->nullable();
            $table->string('no_voyage')->nullable();
            $table->string('no_bl')->nullable();
            $table->unsignedBigInteger('bl_id')->nullable();
            $table->unsignedBigInteger('manifest_id')->nullable();
            
            // System Information
            $table->string('input_by')->nullable();
            $table->datetime('input_date')->nullable();
            $table->string('lokasi')->default('batam');
            $table->boolean('lanjut_muat')->default(false);
            
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
            
            // Lembur/Nginap Information
            $table->boolean('lembur')->default(false)->nullable();
            $table->boolean('nginap')->default(false)->nullable();
            $table->boolean('tidak_lembur_nginap')->default(false)->nullable();
            
            // Timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index(['tanggal_surat_jalan', 'status'], 'sjb_batam_tanggal_status_index');
            $table->index(['no_surat_jalan'], 'sjb_batam_no_sj_index');
            $table->index(['status'], 'sjb_batam_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_jalan_bongkaran_batams');
    }
};
