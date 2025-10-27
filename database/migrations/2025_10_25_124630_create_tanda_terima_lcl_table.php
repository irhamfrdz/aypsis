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
        Schema::create('tanda_terima_lcl', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->string('nomor_tanda_terima')->unique();
            $table->date('tanggal_tanda_terima');
            $table->string('no_surat_jalan_customer')->nullable();
            
            // Term Reference
            $table->foreignId('term_id')->constrained('terms')->onDelete('cascade');
            
            // Receiver Information  
            $table->string('nama_penerima');
            $table->string('pic_penerima')->nullable();
            $table->string('telepon_penerima')->nullable();
            $table->text('alamat_penerima');
            
            // Sender Information
            $table->string('nama_pengirim');
            $table->string('pic_pengirim')->nullable();
            $table->string('telepon_pengirim')->nullable();
            $table->text('alamat_pengirim');
            
            // Goods Information
            $table->string('nama_barang');
            $table->foreignId('jenis_barang_id')->constrained('jenis_barangs')->onDelete('cascade');
            $table->integer('kuantitas')->default(1);
            $table->text('keterangan_barang')->nullable();
            
            // Driver Information
            $table->string('supir');
            $table->string('no_plat');
            
            // Destination
            $table->foreignId('tujuan_pengiriman_id')->constrained('tujuan_kegiatan_utamas')->onDelete('cascade');
            
            // Container Type (always LCL for this table)
            $table->string('tipe_kontainer')->default('lcl');
            
            // Status
            $table->enum('status', ['draft', 'confirmed', 'delivered', 'cancelled'])->default('draft');
            
            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['nomor_tanda_terima']);
            $table->index(['tanggal_tanda_terima']);
            $table->index(['status']);
            $table->index(['tipe_kontainer']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanda_terima_lcl');
    }
};
