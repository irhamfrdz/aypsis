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
        Schema::create('pranota_uang_rit_keneks', function (Blueprint $table) {
            $table->id();
            $table->string('no_pranota')->unique(); // Nomor pranota kenek
            $table->date('tanggal'); // Tanggal pranota
            $table->unsignedBigInteger('surat_jalan_id')->nullable(); // Foreign key ke surat jalan
            $table->unsignedBigInteger('surat_jalan_bongkaran_id')->nullable(); // Foreign key ke surat jalan bongkaran
            $table->text('no_surat_jalan'); // Nomor surat jalan (bisa multiple, dipisah koma)
            $table->text('kenek_nama'); // Nama kenek (bisa multiple, dipisah koma)
            $table->string('no_plat')->nullable(); // Nomor polisi kendaraan
            
            // Kolom uang
            $table->decimal('uang_jalan', 15, 2)->default(0); // Uang jalan
            $table->decimal('uang_rit', 15, 2)->default(0); // Total uang rit
            $table->decimal('uang_rit_kenek', 15, 2)->default(0); // Uang rit khusus kenek
            $table->decimal('total_rit', 15, 2)->default(0); // Total rit
            $table->decimal('total_uang', 15, 2)->default(0); // Total uang (uang_jalan + uang_rit)
            
            // Kolom potongan
            $table->decimal('total_hutang', 15, 2)->default(0); // Total hutang kenek
            $table->decimal('total_tabungan', 15, 2)->default(0); // Total tabungan kenek
            $table->decimal('total_bpjs', 15, 2)->default(0); // Total BPJS kenek
            $table->decimal('grand_total_bersih', 15, 2)->default(0); // Grand total setelah potongan
            
            $table->text('keterangan')->nullable(); // Keterangan tambahan
            
            // Status workflow
            $table->enum('status', ['draft', 'submitted', 'approved', 'paid', 'cancelled'])->default('draft');
            $table->date('tanggal_bayar')->nullable(); // Tanggal pembayaran
            
            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable(); // User yang membuat
            $table->unsignedBigInteger('updated_by')->nullable(); // User yang mengupdate
            $table->unsignedBigInteger('approved_by')->nullable(); // User yang approve
            $table->timestamp('approved_at')->nullable(); // Waktu approve
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('surat_jalan_id')->references('id')->on('surat_jalans')->onDelete('set null');
            $table->foreign('surat_jalan_bongkaran_id')->references('id')->on('surat_jalan_bongkarans')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');

            // Indexes untuk performa
            $table->index(['tanggal', 'status']);
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_uang_rit_keneks');
    }
};
