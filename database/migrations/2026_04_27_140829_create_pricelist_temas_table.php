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
        Schema::create('pricelist_temas', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_biaya');
            $table->string('lokasi')->nullable();
            $table->string('size')->nullable(); // 20ft, 40ft, dll
            $table->decimal('harga', 15, 2);
            $table->enum('status', ['Aktif', 'Non Aktif'])->default('Aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricelist_temas');
    }
};
