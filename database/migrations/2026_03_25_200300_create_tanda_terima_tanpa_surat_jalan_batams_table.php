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
        Schema::create('tanda_terima_tanpa_surat_jalan_batams', function (Blueprint $table) {
            $table->id();
            $table->string('no_tanda_terima')->unique()->nullable();
            $table->date('tanggal_tanda_terima');
            $table->string('nomor_surat_jalan_customer')->nullable();
            $table->string('nomor_tanda_terima')->nullable();
            $table->string('term_id')->nullable(); 
            $table->string('aktifitas')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->string('no_kontainer')->nullable();
            $table->string('size_kontainer')->nullable();
            $table->string('pengirim');
            $table->string('telepon')->nullable();
            $table->string('pic')->nullable();
            $table->string('supir')->nullable();
            $table->string('kenek')->nullable();
            $table->string('no_plat')->nullable();
            $table->string('tujuan_pengiriman');
            $table->string('estimasi_naik_kapal')->nullable();
            $table->string('no_seal')->nullable();
            $table->date('tanggal_seal')->nullable();
            $table->string('penerima');
            $table->string('nama_barang')->nullable();
            $table->text('alamat_pengirim')->nullable();
            $table->text('alamat_penerima')->nullable();
            $table->string('jenis_barang')->nullable();
            $table->integer('jumlah_barang')->default(0);
            $table->string('satuan_barang')->default('unit');
            $table->text('keterangan_barang')->nullable();
            $table->decimal('berat', 10, 2)->nullable();
            $table->string('satuan_berat')->default('kg')->nullable();
            $table->decimal('panjang', 10, 2)->nullable();
            $table->decimal('lebar', 10, 2)->nullable();
            $table->decimal('tinggi', 10, 2)->nullable();
            $table->decimal('meter_kubik', 10, 6)->nullable();
            $table->decimal('tonase', 10, 2)->nullable();
            $table->enum('status', ['draft', 'terkirim', 'diterima', 'selesai'])->default('draft');
            $table->text('catatan')->nullable();
            $table->text('gambar_tanda_terima')->nullable(); // Cast to array in model
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            
            // Asuransi fields from AsuransiManageable trait
            $table->string('asuransi_path')->nullable();
            $table->timestamp('asuransi_uploaded_at')->nullable();
            $table->string('asuransi_uploaded_by')->nullable();
            $table->boolean('is_asuransi_approved')->default(false);
            $table->timestamp('asuransi_approved_at')->nullable();
            $table->string('asuransi_approved_by')->nullable();
            $table->text('asuransi_keterangan')->nullable();
            
            // Dokumen fields
            $table->text('dokumen_ppbj')->nullable();
            $table->text('dokumen_packing_list')->nullable();
            $table->text('dokumen_invoice')->nullable();
            $table->text('dokumen_faktur_pajak')->nullable();
            
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
        Schema::dropIfExists('tanda_terima_tanpa_surat_jalan_batams');
    }
};
