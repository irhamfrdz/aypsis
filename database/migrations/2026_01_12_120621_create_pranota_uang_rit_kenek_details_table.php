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
        // Buat tabel untuk menyimpan detail hutang, tabungan, dan BPJS per kenek per pranota
        Schema::create('pranota_uang_rit_kenek_details', function (Blueprint $table) {
            $table->id();
            $table->string('no_pranota'); // Nomor pranota (group identifier)
            $table->string('kenek_nama'); // Nama kenek
            $table->decimal('total_uang_kenek', 15, 2)->default(0); // Total uang kenek dari semua surat jalan
            $table->decimal('hutang', 15, 2)->default(0); // Hutang per kenek
            $table->decimal('tabungan', 15, 2)->default(0); // Tabungan per kenek
            $table->decimal('bpjs', 15, 2)->default(0); // BPJS per kenek
            $table->decimal('grand_total', 15, 2)->default(0); // Grand total (uang_kenek - hutang - tabungan - bpjs)
            $table->timestamps();

            // Indexes
            $table->index('no_pranota');
            $table->index('kenek_nama');
            $table->unique(['no_pranota', 'kenek_nama']); // Satu record per kenek per pranota
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_uang_rit_kenek_details');
    }
};
