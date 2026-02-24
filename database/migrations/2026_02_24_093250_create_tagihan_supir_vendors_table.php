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
        Schema::create('tagihan_supir_vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_jalan_id')->nullable()->constrained('surat_jalans')->onDelete('cascade');
            $table->string('nama_supir')->nullable();
            $table->string('dari')->nullable();
            $table->string('ke')->nullable();
            $table->string('jenis_kontainer')->nullable();
            $table->decimal('nominal', 15, 2)->default(0);
            $table->string('status_pembayaran')->default('belum_dibayar')->comment('belum_dibayar, sudah_dibayar');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_supir_vendors');
    }
};
