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
        Schema::create('jurnal_entries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pembayaran_aktivitas_lain_id')->unsigned()->nullable();
            $table->date('tanggal');
            $table->bigInteger('akun_coa_id')->unsigned();
            $table->string('akun_kode', 50);
            $table->string('akun_nama');
            $table->decimal('debit', 15, 2)->default(0);
            $table->decimal('kredit', 15, 2)->default(0);
            $table->text('keterangan');
            $table->string('reference_type')->nullable(); // 'pembayaran_aktivitas_lain', 'pembayaran_pranota', etc
            $table->bigInteger('reference_id')->unsigned()->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('pembayaran_aktivitas_lain_id')->references('id')->on('pembayaran_aktivitas_lains')->onDelete('cascade');
            $table->foreign('akun_coa_id')->references('id')->on('akun_coa')->onDelete('cascade');
            
            // Indexes for better performance
            $table->index(['tanggal', 'akun_coa_id']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal_entries');
    }
};
