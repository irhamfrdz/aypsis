<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Master Tipe Kontainer
        if (! Schema::hasTable('sk_tipe_kontainers')) {
            Schema::create('sk_tipe_kontainers', function (Blueprint $table) {
                $table->id();
                $table->string('nama_tipe', 100);
                $table->boolean('status_aktif')->default(true);
                $table->timestamps();
            });
        }

        // 2. Master Ukuran Kontainer
        if (! Schema::hasTable('sk_ukuran_kontainers')) {
            Schema::create('sk_ukuran_kontainers', function (Blueprint $table) {
                $table->id();
                $table->string('deskripsi_ukuran', 50);
                $table->boolean('status_aktif')->default(true);
                $table->timestamps();
            });
        }

        // 3. Master Kontainer
        if (! Schema::hasTable('sk_kontainers')) {
            Schema::create('sk_kontainers', function (Blueprint $table) {
                $table->id();
                $table->string('no_kontainer', 50)->unique();
                $table->foreignId('vendor_id')->constrained('vendor_kontainer_sewas')->onDelete('cascade');
                $table->foreignId('tipe_id')->constrained('sk_tipe_kontainers')->onDelete('cascade');
                $table->foreignId('ukuran_id')->constrained('sk_ukuran_kontainers')->onDelete('cascade');
                $table->boolean('status_aktif')->default(true);
                $table->timestamps();
            });
        }

        // 4. Master Tarif Sewa
        if (! Schema::hasTable('sk_tarif_sewas')) {
            Schema::create('sk_tarif_sewas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('vendor_id')->constrained('vendor_kontainer_sewas')->onDelete('cascade');
                $table->foreignId('tipe_id')->constrained('sk_tipe_kontainers')->onDelete('cascade');
                $table->foreignId('ukuran_id')->constrained('sk_ukuran_kontainers')->onDelete('cascade');
                $table->decimal('tarif_bulanan', 15, 0)->default(0);
                $table->decimal('tarif_harian', 15, 0)->default(0);
                $table->date('tanggal_mulai_berlaku');
                $table->date('tanggal_akhir_berlaku')->nullable();
                $table->boolean('status_aktif')->default(true);
                $table->timestamps();
            });
        }

        // 5. Transaksi Sewa Kontainer
        if (! Schema::hasTable('sk_sewas')) {
            Schema::create('sk_sewas', function (Blueprint $table) {
                $table->id();
                $table->string('no_kontainer', 50);
                $table->foreignId('vendor_id')->constrained('vendor_kontainer_sewas')->onDelete('cascade');
                $table->date('tanggal_sewa');
                $table->date('tanggal_kembali')->nullable();
                $table->decimal('tarif_bulanan', 15, 0)->default(0);
                $table->decimal('tarif_harian', 15, 0)->default(0);
                $table->enum('jenis_tarif', ['Bulanan', 'Harian'])->default('Bulanan');
                $table->enum('status_sewa', ['Aktif', 'Selesai'])->default('Aktif');
                $table->text('catatan')->nullable();
                $table->boolean('non_ppn')->default(false);
                $table->timestamps();

                $table->index('no_kontainer');
                $table->index('status_sewa');
            });
        }

        // 6. Tagihan per Periode Billing
        if (! Schema::hasTable('sk_tagihan_bulans')) {
            Schema::create('sk_tagihan_bulans', function (Blueprint $table) {
                $table->id();
                $table->string('kode_tagihan', 100)->unique()->comment('Format: [NO_KONTAINER][SERIAL_TGL_SEWA][BULAN_KE]');
                $table->foreignId('sewa_id')->constrained('sk_sewas')->onDelete('cascade');
                $table->unsignedInteger('bulan_ke');
                $table->date('tanggal_awal');
                $table->date('tanggal_akhir');
                $table->unsignedInteger('jumlah_hari');
                $table->enum('tipe_tarif', ['BULANAN', 'PRORATE', 'HARIAN'])->default('BULANAN');
                $table->decimal('jumlah_tagihan_estimasi', 15, 0)->default(0)->comment('System-generated estimate');
                $table->decimal('jumlah_tagihan_override', 15, 0)->nullable()->comment('Actual billed amount from vendor');
                $table->enum('status_bayar', ['Belum Ditagih', 'Pranota', 'Belum Bayar', 'Lunas'])->default('Belum Ditagih');
                $table->date('tanggal_tagihan')->nullable();
                $table->date('tanggal_bayar')->nullable();
                $table->string('nomor_invoice', 100)->nullable();
                $table->string('nomor_pranota', 100)->nullable();
                $table->date('tanggal_pranota')->nullable();
                $table->decimal('jumlah_bayar', 15, 0)->nullable();
                $table->decimal('ppn', 15, 0)->nullable()->comment('PPN nominal');
                $table->decimal('pph', 15, 0)->nullable()->comment('PPh 23 nominal');
                $table->string('nomor_bayar', 100)->nullable()->comment('No Bukti Bayar / EBK');
                $table->text('keterangan_selisih')->nullable();
                $table->timestamps();

                $table->index('status_bayar');
                $table->index('sewa_id');
            });
        }

        // 7. Invoice Grup (pengelompokan tagihan dalam satu invoice)
        if (! Schema::hasTable('sk_invoice_grups')) {
            Schema::create('sk_invoice_grups', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_invoice', 100)->unique();
                $table->foreignId('vendor_id')->constrained('vendor_kontainer_sewas')->onDelete('cascade');
                $table->date('tanggal_invoice');
                $table->enum('status_pembayaran', ['Belum Bayar', 'Lunas'])->default('Belum Bayar');
                $table->text('deskripsi')->nullable();
                $table->decimal('adjustment_biaya', 15, 0)->default(0);
                $table->text('adjustment_keterangan')->nullable();
                $table->timestamps();
            });
        }

        // 8. Pivot: Invoice Grup <-> Tagihan Bulan
        if (! Schema::hasTable('sk_invoice_grup_tagihans')) {
            Schema::create('sk_invoice_grup_tagihans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('invoice_grup_id')->constrained('sk_invoice_grups')->onDelete('cascade');
                $table->foreignId('tagihan_bulan_id')->constrained('sk_tagihan_bulans')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['invoice_grup_id', 'tagihan_bulan_id'], 'sk_inv_tag_unique');
            });
        }

        // 9. Add status_aktif to vendor_kontainer_sewas if not exists
        if (Schema::hasTable('vendor_kontainer_sewas') && ! Schema::hasColumn('vendor_kontainer_sewas', 'status_aktif')) {
            Schema::table('vendor_kontainer_sewas', function (Blueprint $table) {
                $table->boolean('status_aktif')->default(true)->after('tax_pph_percent');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sk_invoice_grup_tagihans');
        Schema::dropIfExists('sk_invoice_grups');
        Schema::dropIfExists('sk_tagihan_bulans');
        Schema::dropIfExists('sk_sewas');
        Schema::dropIfExists('sk_tarif_sewas');
        Schema::dropIfExists('sk_kontainers');
        Schema::dropIfExists('sk_ukuran_kontainers');
        Schema::dropIfExists('sk_tipe_kontainers');

        if (Schema::hasColumn('vendor_kontainer_sewas', 'status_aktif')) {
            Schema::table('vendor_kontainer_sewas', function (Blueprint $table) {
                $table->dropColumn('status_aktif');
            });
        }
    }
};
