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
        Schema::create('stock_ban_luar_batams', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke master nama stock ban (optional, if tracking same as stock_bans)
            $table->foreignId('nama_stock_ban_id')->nullable()->constrained('nama_stock_bans')->nullOnDelete();
            
            // Detail ban
            $table->string('nomor_seri')->nullable();
            $table->string('nomor_faktur')->nullable();
            $table->string('nomor_bukti')->nullable();
            $table->string('merk')->nullable();
            $table->string('ukuran')->nullable();
            $table->string('kondisi')->nullable(); // Asli, Kanisir, Afkir dll
            $table->string('status')->default('Stok'); 
            
            // Finansial
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->string('tempat_beli')->nullable();
            
            // Waktu
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->date('tanggal_kembali')->nullable();
            $table->date('tanggal_kirim')->nullable(); // Kapan dikirim ke batam
            
            // Relasi operasional
            $table->string('lokasi')->default('Batam');
            $table->text('keterangan')->nullable();
            
            $table->foreignId('mobil_id')->nullable()->constrained('mobils')->nullOnDelete();
            $table->foreignId('alat_berat_id')->nullable()->constrained('alat_berats')->nullOnDelete();
            $table->foreignId('penerima_id')->nullable()->constrained('karyawans')->nullOnDelete();
            $table->foreignId('kapal_id')->nullable()->constrained('master_kapals')->nullOnDelete();
            
            // Tracking batam/masak
            $table->string('status_ban_luar')->nullable();
            $table->string('status_masak')->default('belum');
            $table->integer('jumlah_masak')->default(0);
            
            // Tracking user
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_ban_luar_batams');
    }
};
