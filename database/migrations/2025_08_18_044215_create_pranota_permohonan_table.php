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
        Schema::create('pranota_permohonan', function (Blueprint $table) {
            $table->foreignId('pranota_supir_id')->constrained('pranota_supirs')->onDelete('cascade');
            $table->foreignId('permohonan_id')->constrained('permohonans')->onDelete('cascade');
            $table->primary(['pranota_supir_id', 'permohonan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_permohonan');
    }
};
