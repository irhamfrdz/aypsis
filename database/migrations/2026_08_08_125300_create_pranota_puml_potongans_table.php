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
        Schema::create('pranota_puml_potongans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pranota_puml_id')->constrained('pranota_pumls')->onDelete('cascade');
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('cascade');
            $table->decimal('pot_utang', 15, 2)->default(0);
            $table->decimal('pot_bpjs', 15, 2)->default(0);
            $table->decimal('pot_pph', 15, 2)->default(0);
            $table->decimal('pot_terlambat', 15, 2)->default(0);
            $table->timestamps();
            
            // A Karyawan can only have one set of adjustments per PUML
            $table->unique(['pranota_puml_id', 'karyawan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_puml_potongans');
    }
};
