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
        if (!Schema::hasTable('permohonan_amprahans')) {
            Schema::create('permohonan_amprahans', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('kapal_id');
                $table->string('nomor_voyage');
                $table->string('status')->default('pending');
            $table->timestamp('tanggal_diterima')->nullable();
                $table->text('keterangan_umum')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('permohonan_amprahan_items')) {
            Schema::create('permohonan_amprahan_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('permohonan_id');
                $table->string('nama_barang');
                $table->decimal('jumlah', 10, 2);
                $table->string('satuan');
                $table->text('keterangan')->nullable();
                
                $table->foreign('permohonan_id')
                      ->references('id')
                      ->on('permohonan_amprahans')
                      ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_amprahan_items');
        Schema::dropIfExists('permohonan_amprahans');
    }
};
