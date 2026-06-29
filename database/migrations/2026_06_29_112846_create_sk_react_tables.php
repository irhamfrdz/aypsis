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
        Schema::create('sk_customers', function (Blueprint $table) {
            $table->string('id_customer', 100)->primary();
            $table->string('nama_customer');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('sk_tipes', function (Blueprint $table) {
            $table->string('id_tipe', 100)->primary();
            $table->string('nama_tipe');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('sk_ukurans', function (Blueprint $table) {
            $table->string('id_ukuran', 100)->primary();
            $table->string('deskripsi_ukuran');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('sk_kontainers', function (Blueprint $table) {
            $table->string('no_kontainer', 100)->primary();
            $table->string('id_customer', 100);
            $table->string('id_tipe', 100);
            $table->string('id_ukuran', 100);
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();

            $table->foreign('id_customer')->references('id_customer')->on('sk_customers')->onDelete('cascade');
            $table->foreign('id_tipe')->references('id_tipe')->on('sk_tipes')->onDelete('cascade');
            $table->foreign('id_ukuran')->references('id_ukuran')->on('sk_ukurans')->onDelete('cascade');
        });

        Schema::create('sk_tarifs', function (Blueprint $table) {
            $table->string('id_tarif', 100)->primary();
            $table->string('id_customer', 100);
            $table->string('id_tipe', 100);
            $table->string('id_ukuran', 100);
            $table->decimal('tarif_bulanan', 15, 2);
            $table->decimal('tarif_harian', 15, 2);
            $table->date('tanggal_mulai_berlaku');
            $table->date('tanggal_akhir_berlaku')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();

            $table->foreign('id_customer')->references('id_customer')->on('sk_customers')->onDelete('cascade');
            $table->foreign('id_tipe')->references('id_tipe')->on('sk_tipes')->onDelete('cascade');
            $table->foreign('id_ukuran')->references('id_ukuran')->on('sk_ukurans')->onDelete('cascade');
        });

        Schema::create('sk_sewas', function (Blueprint $table) {
            $table->string('id_sewa', 100)->primary();
            $table->string('no_kontainer', 100);
            $table->string('id_customer', 100);
            $table->date('tanggal_sewa');
            $table->date('tanggal_kembali')->nullable();
            $table->decimal('tarif_bulanan', 15, 2);
            $table->decimal('tarif_harian', 15, 2);
            $table->string('jenis_tarif', 50); // 'Bulanan' | 'Harian'
            $table->string('status_sewa', 50); // 'Aktif' | 'Selesai'
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('no_kontainer')->references('no_kontainer')->on('sk_kontainers')->onDelete('cascade');
            $table->foreign('id_customer')->references('id_customer')->on('sk_customers')->onDelete('cascade');
        });

        Schema::create('sk_tagihan_bulans', function (Blueprint $table) {
            $table->string('id_tagihan', 100)->primary();
            $table->string('id_sewa', 100)->nullable();
            $table->integer('bulan_ke')->nullable();
            $table->date('tanggal_awal')->nullable();
            $table->date('tanggal_akhir')->nullable();
            $table->integer('jumlah_hari')->nullable();
            $table->string('tipe_tarif', 50)->nullable();
            $table->decimal('jumlah_tagihan', 15, 2)->nullable();
            $table->string('status_bayar', 50)->nullable();
            $table->date('tanggal_tagihan')->nullable();
            $table->date('tanggal_bayar')->nullable();
            $table->string('nomor_invoice_grup', 100)->nullable();
            $table->string('nomor_pranota', 100)->nullable();
            $table->date('tanggal_pranota')->nullable();
            $table->decimal('jumlah_tagihan_override', 15, 2)->nullable();
            $table->decimal('jumlah_bayar', 15, 2)->nullable();
            $table->decimal('selisih_pembayaran', 15, 2)->nullable();
            $table->text('keterangan_selisih')->nullable();
            $table->decimal('ppn', 15, 2)->nullable();
            $table->decimal('pph', 15, 2)->nullable();
            $table->string('nomor_bayar', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('sk_invoice_grups', function (Blueprint $table) {
            $table->string('nomor_invoice', 100)->primary();
            $table->string('id_customer', 100);
            $table->date('tanggal_invoice');
            $table->string('status_pembayaran', 50);
            $table->text('deskripsi');
            $table->json('list_id_tagihan');
            $table->decimal('adjustment_biaya', 15, 2)->nullable();
            $table->text('adjustment_keterangan')->nullable();
            $table->timestamps();

            $table->foreign('id_customer')->references('id_customer')->on('sk_customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sk_invoice_grups');
        Schema::dropIfExists('sk_tagihan_bulans');
        Schema::dropIfExists('sk_sewas');
        Schema::dropIfExists('sk_tarifs');
        Schema::dropIfExists('sk_kontainers');
        Schema::dropIfExists('sk_ukurans');
        Schema::dropIfExists('sk_tipes');
        Schema::dropIfExists('sk_customers');
    }
};
