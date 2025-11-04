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
        Schema::dropIfExists('tarif_batam');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate table structure in case of rollback
        Schema::create('tarif_batam', function (Blueprint $table) {
            $table->id();
            $table->string('chasis_ayp');
            $table->decimal('20ft_full', 15, 2);
            $table->decimal('20ft_empty', 15, 2);
            $table->decimal('antar_lokasi', 15, 2);
            $table->decimal('40ft_full', 15, 2);
            $table->decimal('40ft_empty', 15, 2);
            $table->decimal('40ft_antar_lokasi', 15, 2);
            $table->date('masa_berlaku');
            $table->timestamps();
        });
    }
};
