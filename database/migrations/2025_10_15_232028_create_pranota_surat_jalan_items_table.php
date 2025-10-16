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
        Schema::create('pranota_surat_jalan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pranota_surat_jalan_id')->constrained('pranota_surat_jalans')->onDelete('cascade');
            $table->foreignId('surat_jalan_id')->constrained('surat_jalans')->onDelete('cascade');
            $table->timestamps();
            
            // Ensure unique combination
            $table->unique(['pranota_surat_jalan_id', 'surat_jalan_id'], 'unique_pranota_surat_jalan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_surat_jalan_items');
    }
};
