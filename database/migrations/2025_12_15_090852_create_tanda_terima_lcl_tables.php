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
        // 1. Main table untuk Tanda Terima LCL
        Schema::create('tanda_terimas_lcl', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_tanda_terima')->nullable()->unique();
            $table->date('tanggal_tanda_terima');
            $table->string('no_surat_jalan_customer')->nullable();
            $table->foreignId('term_id')->nullable()->constrained('terms')->onDelete('set null');
            
            // Informasi Kontainer
            $table->string('nomor_kontainer')->nullable()->index(); // Important: index untuk query pivot
            $table->string('size_kontainer')->nullable(); // 20ft, 40ft, 40hc, 45ft
            $table->enum('tipe_kontainer', ['fcl', 'lcl', 'cargo'])->default('lcl');
            
            // Informasi Barang (bisa berbeda per tanda terima dalam 1 kontainer)
            $table->string('nama_barang')->nullable();
            $table->text('keterangan_barang')->nullable();
            
            // Informasi Supir
            $table->string('supir')->nullable();
            $table->string('no_plat')->nullable();
            
            // Tujuan Pengiriman
            $table->string('tujuan_pengiriman')->nullable();
            $table->foreignId('master_tujuan_kirim_id')->nullable()->constrained('master_tujuan_kirim')->onDelete('set null');
            
            // Upload Gambar
            $table->json('gambar_surat_jalan')->nullable(); // Array of image paths
            
            // Status & Tracking
            $table->enum('status', ['draft', 'submitted', 'completed'])->default('draft');
            $table->string('kegiatan')->nullable();
            
            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('tanggal_tanda_terima');
            $table->index('status');
            $table->index('created_at');
        });

        // 2. Table untuk Items/Dimensi per Tanda Terima LCL
        Schema::create('tanda_terima_lcl_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanda_terima_lcl_id')->constrained('tanda_terimas_lcl')->onDelete('cascade');
            
            // Dimensi
            $table->string('nama_barang')->nullable();
            $table->text('keterangan_barang')->nullable();
            $table->integer('jumlah_koli')->nullable();
            $table->decimal('panjang', 10, 3)->nullable(); // meter
            $table->decimal('lebar', 10, 3)->nullable(); // meter
            $table->decimal('tinggi', 10, 3)->nullable(); // meter
            $table->decimal('meter_kubik', 12, 3)->nullable(); // m³ (calculated)
            $table->decimal('tonase', 10, 3)->nullable(); // ton
            
            $table->timestamps();
            
            // Index
            $table->index('tanda_terima_lcl_id');
        });

        // 3. Pivot table untuk Penerima (Many-to-Many)
        Schema::create('tanda_terima_lcl_penerima', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanda_terima_lcl_id')->constrained('tanda_terimas_lcl')->onDelete('cascade');
            
            // Data penerima
            $table->string('nama_penerima');
            $table->string('pic_penerima')->nullable();
            $table->string('telepon_penerima')->nullable();
            $table->text('alamat_penerima');
            
            // Order/sequence untuk multiple penerima
            $table->integer('urutan')->default(1);
            
            $table->timestamps();
            
            // Indexes
            $table->index('tanda_terima_lcl_id');
            $table->index('nama_penerima');
        });

        // 4. Pivot table untuk Pengirim (Many-to-Many)
        Schema::create('tanda_terima_lcl_pengirim', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tanda_terima_lcl_id')->constrained('tanda_terimas_lcl')->onDelete('cascade');
            
            // Data pengirim
            $table->string('nama_pengirim');
            $table->string('pic_pengirim')->nullable();
            $table->string('telepon_pengirim')->nullable();
            $table->text('alamat_pengirim');
            
            // Order/sequence untuk multiple pengirim
            $table->integer('urutan')->default(1);
            
            $table->timestamps();
            
            // Indexes
            $table->index('tanda_terima_lcl_id');
            $table->index('nama_pengirim');
        });

        // 5. Pivot table untuk Kontainer - Tanda Terima (untuk track 1 kontainer = multiple tanda terima)
        Schema::create('kontainer_tanda_terima_lcl', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kontainer')->index(); // Nomor kontainer fisik
            $table->foreignId('tanda_terima_lcl_id')->constrained('tanda_terimas_lcl')->onDelete('cascade');
            
            // Metadata untuk tracking
            $table->integer('urutan_dalam_kontainer')->default(1); // Tanda terima ke-berapa dalam kontainer ini
            $table->decimal('persentase_volume', 5, 2)->nullable(); // Persentase dari total volume kontainer
            $table->text('catatan')->nullable();
            
            $table->timestamps();
            
            // Composite index untuk query efficiency - dengan nama custom yang lebih pendek
            $table->index(['nomor_kontainer', 'tanda_terima_lcl_id'], 'idx_kontainer_tt_lcl');
            $table->unique(['nomor_kontainer', 'tanda_terima_lcl_id'], 'uq_kontainer_tt_lcl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontainer_tanda_terima_lcl');
        Schema::dropIfExists('tanda_terima_lcl_pengirim');
        Schema::dropIfExists('tanda_terima_lcl_penerima');
        Schema::dropIfExists('tanda_terima_lcl_items');
        Schema::dropIfExists('tanda_terimas_lcl');
    }
};
