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
        Schema::create('pembayaran_uang_muka_supir_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_id'); // FK ke pembayaran_aktivitas_lainnya
            $table->string('nama_supir'); // Nama supir dari karyawan
            $table->decimal('jumlah_uang_muka', 15, 2); // Jumlah uang muka
            $table->text('keterangan')->nullable(); // Keterangan tambahan
            $table->enum('status', ['pending', 'dibayar', 'lunas'])->default('dibayar'); // Status pembayaran
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('pembayaran_id')
                  ->references('id')
                  ->on('pembayaran_aktivitas_lainnya')
                  ->onDelete('cascade');

            // Index untuk performa query
            $table->index('pembayaran_id');
            $table->index('nama_supir');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_uang_muka_supir_details');
    }
};
