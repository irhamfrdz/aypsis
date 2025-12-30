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
        Schema::create('kontainer_perjalanans', function (Blueprint $table) {
            $table->id();
            
            // Foreign key ke surat_jalans
            $table->unsignedBigInteger('surat_jalan_id');
            $table->foreign('surat_jalan_id')
                  ->references('id')
                  ->on('surat_jalans')
                  ->onDelete('cascade');
            
            // Informasi kontainer
            $table->string('no_kontainer')->nullable();
            $table->string('no_surat_jalan')->nullable();
            $table->string('tipe_kontainer')->nullable();
            $table->string('ukuran')->nullable();
            
            // Informasi pengiriman
            $table->string('tujuan_pengiriman')->nullable();
            $table->string('supir')->nullable();
            $table->string('no_plat')->nullable();
            
            // Waktu tracking
            $table->dateTime('waktu_keluar')->nullable();
            $table->dateTime('estimasi_waktu_tiba')->nullable();
            $table->dateTime('waktu_tiba_aktual')->nullable();
            
            // Status tracking
            $table->enum('status', [
                'dalam_perjalanan', 
                'sampai_tujuan', 
                'dibatalkan'
            ])->default('dalam_perjalanan');
            
            // Informasi tambahan
            $table->text('catatan_keluar')->nullable();
            $table->text('catatan_tiba')->nullable();
            $table->string('lokasi_terakhir')->nullable();
            
            // Koordinat GPS (opsional untuk tracking real-time)
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // User tracking
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes untuk performance
            $table->index('surat_jalan_id');
            $table->index('no_kontainer');
            $table->index('status');
            $table->index('waktu_keluar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontainer_perjalanans');
    }
};
