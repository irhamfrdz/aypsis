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
        Schema::create('tarif_batam', function (Blueprint $table) {
            $table->id();
            $table->decimal('chasis_ayp', 15, 2)->nullable()->comment('Tarif Chasis AYP');
            $table->decimal('20ft_full', 15, 2)->nullable()->comment('Tarif 20ft Full');
            $table->decimal('20ft_empty', 15, 2)->nullable()->comment('Tarif 20ft Empty');
            $table->decimal('antar_lokasi', 15, 2)->nullable()->comment('Tarif Antar Lokasi');
            $table->decimal('40ft_full', 15, 2)->nullable()->comment('Tarif 40ft Full');
            $table->decimal('40ft_empty', 15, 2)->nullable()->comment('Tarif 40ft Empty');
            $table->decimal('40ft_antar_lokasi', 15, 2)->nullable()->comment('Tarif 40ft Antar Lokasi');
            $table->date('masa_berlaku')->comment('Masa Berlaku Tarif');
            $table->text('keterangan')->nullable()->comment('Keterangan Tambahan');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->comment('Status Tarif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarif_batam');
    }
};
