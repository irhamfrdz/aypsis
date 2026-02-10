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
        Schema::create('master_pricelist_biaya_trucking', function (Blueprint $table) {
            $table->id();
            $table->string('rute'); // Contoh: Jakarta - Bandung
            $table->string('tujuan')->nullable(); // Tujuan spesifik
            $table->string('jenis_kendaraan')->nullable(); // Truck Fuso, Tronton, dll
            $table->decimal('biaya', 15, 2); // Biaya trucking
            $table->string('satuan')->default('trip'); // trip, rit, km, dll
            $table->date('tanggal_berlaku')->nullable(); // Tanggal mulai berlaku
            $table->date('tanggal_berakhir')->nullable(); // Tanggal berakhir
            $table->text('keterangan')->nullable();
            $table->enum('status', ['aktif', 'non-aktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_pricelist_biaya_trucking');
    }
};
