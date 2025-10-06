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
        Schema::create('pranota_supirs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota')->unique();
            $table->date('tanggal_pranota');
            $table->decimal('total_biaya_memo', 15, 2);
            $table->decimal('adjustment', 15, 2)->nullable();
            $table->string('alasan_adjustment')->nullable();
            $table->decimal('total_biaya_pranota', 15, 2);
            $table->text('catatan')->nullable();
            $table->string('status_pembayaran')->default('Belum Lunas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_supirs');
    }
};
