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
        Schema::create('pricelist_uang_jalan_batam', function (Blueprint $table) {
            $table->id();
            $table->string('expedisi'); // ATB, AYP, dll
            $table->string('ring'); // 1, 2, 3, 4, 5
            $table->string('size'); // 20FT, 40FT, 45FT
            $table->enum('f_e', ['Full', 'Empty']); // Full atau Empty
            $table->decimal('tarif', 15, 2); // Tarif dalam rupiah
            $table->enum('status', ['AQUA', 'CHASIS PB'])->nullable(); // Status opsional
            $table->timestamps();
            
            // Index untuk pencarian cepat
            $table->index(['expedisi', 'ring', 'size', 'f_e']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricelist_uang_jalan_batam');
    }
};
