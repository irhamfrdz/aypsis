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
        Schema::create('surat_jalan_batams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_batam_id')->nullable();
            $table->unsignedBigInteger('penerima_id')->nullable();
            $table->unsignedBigInteger('notify_party_id')->nullable();
            $table->string('alamat_penerima')->nullable();
            $table->date('tanggal_surat_jalan');
            $table->string('no_surat_jalan')->unique();
            $table->string('kegiatan')->nullable();
            $table->string('pengirim')->nullable();
            $table->text('alamat')->nullable();
            $table->string('telp')->nullable();
            $table->string('jenis_barang')->nullable();
            $table->string('tujuan_pengambilan')->nullable();
            $table->string('retur_barang')->nullable();
            $table->integer('jumlah_retur')->default(0);
            $table->string('supir')->nullable();
            $table->string('supir2')->nullable();
            $table->string('no_plat')->nullable();
            $table->string('kenek')->nullable();
            $table->string('krani')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->string('no_kontainer')->nullable();
            $table->string('no_seal')->nullable();
            $table->string('size')->nullable();
            $table->integer('jumlah_kontainer')->default(1);
            $table->integer('karton')->default(0);
            $table->integer('plastik')->default(0);
            $table->integer('terpal')->default(0);
            $table->timestamp('waktu_berangkat')->nullable();
            $table->string('tujuan_pengiriman')->nullable();
            $table->date('tanggal_muat')->nullable();
            $table->time('jam_berangkat')->nullable();
            $table->string('term')->nullable();
            $table->text('aktifitas')->nullable();
            $table->string('rit')->nullable();
            $table->decimal('uang_jalan', 15, 2)->default(0);
            $table->decimal('tarif', 15, 2)->default(0);
            $table->string('no_pemesanan')->nullable();
            $table->string('gambar')->nullable();
            $table->string('gambar_checkpoint')->nullable();
            $table->unsignedBigInteger('input_by')->nullable();
            $table->timestamp('input_date')->nullable();
            $table->enum('status', ['draft', 'active', 'completed', 'cancelled', 'belum masuk checkpoint', 'sudah_checkpoint'])->default('draft');
            $table->string('status_pembayaran')->nullable();
            $table->string('status_pembayaran_uang_jalan')->nullable();
            $table->string('status_pembayaran_uang_rit')->nullable();
            $table->string('status_pembayaran_uang_rit_kenek')->nullable();
            $table->date('tanggal_tanda_terima')->nullable();
            $table->decimal('total_tarif', 15, 2)->default(0);
            $table->decimal('jumlah_terbayar', 15, 2)->default(0);
            $table->decimal('uang_rit_kenek', 15, 2)->default(0);
            $table->unsignedBigInteger('gate_in_id')->nullable();
            $table->string('status_gate_in')->nullable();
            $table->date('tanggal_gate_in')->nullable();
            $table->text('catatan_gate_in')->nullable();
            $table->text('catatan_checkpoint')->nullable();
            $table->date('tanggal_checkpoint')->nullable();
            $table->boolean('is_supir_customer')->default(false);
            $table->boolean('lembur')->default(false);
            $table->boolean('nginap')->default(false);
            $table->boolean('tidak_lembur_nginap')->default(false);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('order_batam_id')->references('id')->on('order_batams')->onDelete('cascade');
            $table->foreign('input_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['tanggal_surat_jalan', 'status']);
            $table->index(['no_surat_jalan']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_jalan_batams');
    }
};
