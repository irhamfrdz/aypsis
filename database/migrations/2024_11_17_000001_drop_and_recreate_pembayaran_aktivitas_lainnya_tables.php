<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Migration untuk drop dan recreate tabel pembayaran_aktivitas_lainnya
     * dengan struktur yang sesuai form baru
     */
    public function up(): void
    {
        // Disable foreign key checks untuk drop tables
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Drop tabel lama jika ada
        Schema::dropIfExists('pembayaran_uang_muka_supir_details');
        Schema::dropIfExists('pembayaran_aktivitas_lainnya_supir');
        Schema::dropIfExists('pembayaran_aktivitas_lainnya_items');
        Schema::dropIfExists('pembayaran_aktivitas_lainnya');

        // Re-enable foreign key checks
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create tabel pembayaran_aktivitas_lainnya dengan struktur baru
        Schema::create('pembayaran_aktivitas_lainnya', function (Blueprint $table) {
            $table->id();
            
            // Informasi Pembayaran Dasar
            $table->string('nomor_pembayaran', 50)->unique()->comment('Format: PMS1116000001');
            $table->string('nomor_accurate', 100)->nullable()->comment('Nomor referensi dari sistem Accurate');
            $table->date('tanggal_pembayaran')->index();
            
            // Informasi Voyage
            $table->string('nomor_voyage', 100)->nullable()->comment('Nomor voyage dari tabel naik_kapal atau bls');
            $table->string('nama_kapal', 200)->nullable()->comment('Nama kapal terkait voyage');
            
            // Informasi Pembayaran
            $table->decimal('total_pembayaran', 15, 2)->default(0)->comment('Total pembayaran/uang muka');
            $table->text('aktivitas_pembayaran')->comment('Deskripsi aktivitas pembayaran (wajib diisi)');
            
            // Plat Nomor (untuk KIR & STNK)
            $table->string('plat_nomor', 50)->nullable()->comment('Plat nomor untuk kegiatan KIR & STNK');
            
            // Akun Bank dan Biaya
            $table->unsignedBigInteger('pilih_bank')->nullable()->comment('ID dari master_coa (kategori Bank/Kas)');
            $table->unsignedBigInteger('akun_biaya_id')->nullable()->comment('ID dari master_coa (tipe BIAYA)');
            
            // Jenis Transaksi
            $table->enum('jenis_transaksi', ['debit', 'kredit'])->default('kredit')->comment('Jenis transaksi: debit (pemasukan) atau kredit (pengeluaran)');
            
            // Jenis Pembayaran
            $table->boolean('is_dp')->default(false)->comment('Apakah ini pembayaran DP/uang muka');
            
            // Status dan Approval
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'paid'])->default('draft')->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['status', 'tanggal_pembayaran']);
            $table->index('nomor_voyage');
            $table->index('created_by');
            
            // Foreign keys (optional, jika tabel master_coa dan users ada)
            // $table->foreign('pilih_bank')->references('id')->on('master_coa')->onDelete('set null');
            // $table->foreign('akun_biaya_id')->references('id')->on('master_coa')->onDelete('set null');
            // $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            // $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });

        // Create tabel pembayaran_aktivitas_lainnya_supir (untuk daftar uang muka supir)
        Schema::create('pembayaran_aktivitas_lainnya_supir', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_id')->comment('FK ke pembayaran_aktivitas_lainnya');
            $table->unsignedBigInteger('supir_id')->comment('FK ke master_supir');
            $table->decimal('jumlah_uang_muka', 15, 2)->default(0)->comment('Jumlah uang muka untuk supir ini');
            $table->text('keterangan')->nullable()->comment('Keterangan tambahan untuk uang muka supir');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('pembayaran_id')
                  ->references('id')
                  ->on('pembayaran_aktivitas_lainnya')
                  ->onDelete('cascade');
            
            // Index
            $table->index('pembayaran_id');
            $table->index('supir_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_aktivitas_lainnya_supir');
        Schema::dropIfExists('pembayaran_aktivitas_lainnya_items');
        Schema::dropIfExists('pembayaran_aktivitas_lainnya');
    }
};
