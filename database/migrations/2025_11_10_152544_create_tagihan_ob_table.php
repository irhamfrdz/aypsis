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
        Schema::create('tagihan_ob', function (Blueprint $table) {
            $table->id();
            $table->string('kapal');
            $table->string('voyage');
            $table->string('nomor_kontainer');
            $table->string('nama_supir');
            $table->string('barang');
            $table->enum('status_kontainer', ['full', 'empty'])->comment('full = tarik isi, empty = tarik kosong');
            $table->string('size_kontainer')->nullable()->comment('20ft, 40ft, dll');
            $table->decimal('biaya', 15, 2)->default(0);
            $table->unsignedBigInteger('bl_id')->nullable()->comment('Referensi ke tabel bls');
            $table->unsignedBigInteger('created_by')->nullable()->comment('User yang membuat record');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['kapal', 'voyage']);
            $table->index('nomor_kontainer');
            $table->index('nama_supir');
            $table->index('status_kontainer');
            $table->index('created_by');
            $table->index('bl_id');
            
            // Foreign keys
            $table->foreign('bl_id')->references('id')->on('bls')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_ob');
    }
};
