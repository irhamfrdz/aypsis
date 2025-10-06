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
        Schema::create('crew_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('cascade');
            $table->string('item_name'); // Nama item kelengkapan (CV, E-KTP, etc.)
            $table->enum('status', ['ada', 'tidak'])->default('tidak'); // ADA/TIDAK
            $table->string('nomor_sertifikat')->nullable(); // Nomor sertifikat
            $table->date('issued_date')->nullable(); // Tanggal terbit
            $table->date('expired_date')->nullable(); // Tanggal expired
            $table->text('catatan')->nullable(); // Catatan tambahan
            $table->timestamps();

            // Index untuk performa
            $table->index(['karyawan_id', 'item_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crew_checklists');
    }
};
