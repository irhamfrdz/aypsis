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
        Schema::create('sewa_customers', function (Blueprint $table) {
            $table->string('id_customer')->primary();
            $table->string('nama_customer');
            $table->timestamps();
        });

        Schema::create('sewa_tipes', function (Blueprint $table) {
            $table->string('id_tipe')->primary();
            $table->string('nama_tipe');
            $table->timestamps();
        });

        Schema::create('sewa_ukurans', function (Blueprint $table) {
            $table->string('id_ukuran')->primary();
            $table->string('deskripsi_ukuran');
            $table->timestamps();
        });

        Schema::create('sewa_kontainers', function (Blueprint $table) {
            $table->string('no_kontainer')->primary();
            $table->string('id_customer');
            $table->string('id_tipe');
            $table->string('id_ukuran');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();

            $table->foreign('id_customer')->references('id_customer')->on('sewa_customers')->onDelete('cascade');
            $table->foreign('id_tipe')->references('id_tipe')->on('sewa_tipes')->onDelete('cascade');
            $table->foreign('id_ukuran')->references('id_ukuran')->on('sewa_ukurans')->onDelete('cascade');
        });

        Schema::create('sewa_tarifs', function (Blueprint $table) {
            $table->string('id_tarif')->primary();
            $table->string('id_customer');
            $table->string('id_tipe');
            $table->string('id_ukuran');
            $table->decimal('tarif_bulanan', 15, 2)->default(0);
            $table->decimal('tarif_harian', 15, 2)->default(0);
            $table->date('tanggal_mulai_berlaku');
            $table->date('tanggal_akhir_berlaku')->nullable();
            $table->timestamps();

            $table->foreign('id_customer')->references('id_customer')->on('sewa_customers')->onDelete('cascade');
            $table->foreign('id_tipe')->references('id_tipe')->on('sewa_tipes')->onDelete('cascade');
            $table->foreign('id_ukuran')->references('id_ukuran')->on('sewa_ukurans')->onDelete('cascade');
        });

        Schema::create('sewa_transaksis', function (Blueprint $table) {
            $table->string('id_sewa')->primary();
            $table->string('no_kontainer');
            $table->string('id_customer');
            $table->date('tanggal_sewa');
            $table->date('tanggal_kembali')->nullable();
            $table->decimal('tarif_bulanan', 15, 2)->default(0);
            $table->decimal('tarif_harian', 15, 2)->default(0);
            $table->string('jenis_tarif'); // Bulanan, Harian
            $table->string('status_sewa'); // Aktif, Selesai
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('no_kontainer')->references('no_kontainer')->on('sewa_kontainers')->onDelete('cascade');
            $table->foreign('id_customer')->references('id_customer')->on('sewa_customers')->onDelete('cascade');
        });

        Schema::create('sewa_invoices', function (Blueprint $table) {
            $table->string('nomor_invoice')->primary();
            $table->string('id_customer');
            $table->date('tanggal_invoice');
            $table->string('status_pembayaran'); // Belum Bayar, Lunas
            $table->text('deskripsi')->nullable();
            $table->decimal('adjustment_biaya', 15, 2)->default(0);
            $table->string('adjustment_keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_customer')->references('id_customer')->on('sewa_customers')->onDelete('cascade');
        });

        Schema::create('sewa_tagihans', function (Blueprint $table) {
            $table->string('id_tagihan')->primary();
            $table->string('id_sewa');
            $table->integer('bulan_ke');
            $table->date('tanggal_awal');
            $table->date('tanggal_akhir');
            $table->integer('jumlah_hari');
            $table->string('tipe_tarif'); // BULANAN, PRORATE, HARIAN
            $table->decimal('jumlah_tagihan', 15, 2)->default(0);
            $table->string('status_bayar'); // Belum Ditagih, Pranota, Belum Bayar, Lunas
            $table->date('tanggal_tagihan')->nullable();
            $table->date('tanggal_bayar')->nullable();
            $table->string('nomor_invoice_grup')->nullable();
            $table->decimal('jumlah_tagihan_override', 15, 2)->nullable();
            $table->decimal('jumlah_bayar', 15, 2)->nullable();
            $table->decimal('selisih_pembayaran', 15, 2)->nullable();
            $table->string('keterangan_selisih')->nullable();
            $table->decimal('ppn', 15, 2)->nullable();
            $table->decimal('pph', 15, 2)->nullable();
            $table->string('nomor_bayar')->nullable();
            $table->timestamps();

            $table->foreign('id_sewa')->references('id_sewa')->on('sewa_transaksis')->onDelete('cascade');
            $table->foreign('nomor_invoice_grup')->references('nomor_invoice')->on('sewa_invoices')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sewa_tagihans');
        Schema::dropIfExists('sewa_invoices');
        Schema::dropIfExists('sewa_transaksis');
        Schema::dropIfExists('sewa_tarifs');
        Schema::dropIfExists('sewa_kontainers');
        Schema::dropIfExists('sewa_ukurans');
        Schema::dropIfExists('sewa_tipes');
        Schema::dropIfExists('sewa_customers');
    }
};
