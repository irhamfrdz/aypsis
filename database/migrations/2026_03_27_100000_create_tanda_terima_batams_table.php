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
        Schema::create('tanda_terima_batams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_jalan_batam_id')->nullable()->constrained('surat_jalan_batams')->onDelete('cascade');

            // Data dari surat jalan
            $table->string('no_surat_jalan');
            $table->date('tanggal_surat_jalan')->nullable();
            $table->string('supir')->nullable();
            $table->string('supir_pengganti')->nullable();
            $table->string('no_plat')->nullable();
            $table->string('kegiatan')->nullable();
            $table->string('jenis_barang')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->json('tipe_kontainer_details')->nullable();
            $table->string('size')->nullable();
            $table->integer('jumlah_kontainer')->default(1);
            $table->text('no_kontainer')->nullable();
            $table->json('kontainer_details')->nullable();
            $table->string('no_seal')->nullable();
            $table->string('tujuan_pengiriman')->nullable();
            $table->string('pengirim')->nullable();
            $table->string('penerima')->nullable();
            $table->text('alamat_penerima')->nullable();
            $table->string('gambar_checkpoint')->nullable();

            // Kolom tambahan untuk tanda terima
            $table->string('estimasi_nama_kapal')->nullable();
            $table->string('nomor_ro')->nullable();
            $table->date('expired_date')->nullable();
            $table->string('nomor_performa')->nullable();
            $table->date('tanggal')->nullable();
            $table->date('tanggal_ambil_kontainer')->nullable();
            $table->date('tanggal_checkpoint_supir')->nullable();
            $table->date('tanggal_terima_pelabuhan')->nullable();
            $table->date('tanggal_garasi')->nullable();
            $table->integer('jumlah')->nullable();
            $table->string('satuan')->nullable();
            $table->decimal('panjang', 10, 3)->nullable();
            $table->decimal('lebar', 10, 3)->nullable();
            $table->decimal('tinggi', 10, 3)->nullable();
            $table->decimal('meter_kubik', 10, 3)->nullable();
            $table->decimal('tonase', 10, 3)->nullable();
            $table->json('dimensi_details')->nullable();
            $table->json('nama_barang')->nullable();

            // Metadata
            $table->text('catatan')->nullable();
            $table->boolean('lembur')->default(false);
            $table->boolean('nginap')->default(false);
            $table->boolean('tidak_lembur_nginap')->default(false);
            $table->string('term')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            // JSON fields from standard tanda_terima
            $table->json('dimensi_items')->nullable();

            // Asuransi fields from AsuransiManageable trait
            $table->string('asuransi_path')->nullable();
            $table->timestamp('asuransi_uploaded_at')->nullable();
            $table->string('asuransi_uploaded_by')->nullable();
            $table->boolean('is_asuransi_approved')->default(false);
            $table->timestamp('asuransi_approved_at')->nullable();
            $table->string('asuransi_approved_by')->nullable();
            $table->text('asuransi_keterangan')->nullable();

            // Dokumen fields
            $table->json('dokumen_ppbj')->nullable();
            $table->json('dokumen_packing_list')->nullable();
            $table->json('dokumen_invoice')->nullable();
            $table->json('dokumen_faktur_pajak')->nullable();

            // Indexes
            $table->index('no_surat_jalan');
            $table->index('tanggal_surat_jalan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanda_terima_batams');
    }
};
