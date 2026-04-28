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
        Schema::create('surat_jalan_tarik_kosong_batams', function (Blueprint $table) {
            $table->id();
            
            // Basic Information
            $table->date('tanggal_surat_jalan');
            $table->string('no_surat_jalan')->unique();
            $table->string('no_tiket_do')->nullable();
            
            // Sender/Recipient Information
            $table->string('pengirim')->nullable();
            $table->string('penerima')->nullable();
            $table->text('alamat')->nullable();
            
            // Cargo Information
            $table->string('tujuan_pengambilan')->nullable();
            $table->string('tujuan_pengiriman')->nullable();
            
            // Personnel Information
            $table->string('supir')->nullable();
            $table->string('supir2')->nullable();
            $table->string('no_plat')->nullable();
            $table->string('kenek')->nullable();
            
            // Container Information
            $table->string('tipe_kontainer')->nullable();
            $table->string('no_kontainer')->nullable();
            $table->string('size')->nullable();
            $table->string('f_e')->default('E'); // Default to Empty for Tarik Kosong
            
            // Financial Information
            $table->decimal('uang_jalan', 15, 2)->default(0);
            $table->string('status_pembayaran_uang_jalan')->nullable();
            
            // System Information
            $table->unsignedBigInteger('input_by')->nullable();
            $table->datetime('input_date')->nullable();
            $table->string('lokasi')->default('batam');
            
            // Status and Activities
            $table->enum('status', [
                'draft', 
                'active', 
                'completed', 
                'cancelled'
            ])->default('draft');
            $table->text('catatan')->nullable();
            
            // Timestamps
            $table->timestamps();

            // Foreign keys
            $table->foreign('input_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['tanggal_surat_jalan', 'status']);
            $table->index(['no_surat_jalan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_jalan_tarik_kosong_batams');
    }
};
