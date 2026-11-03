<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pranota_gaji_supir_batams', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota')->unique();
            $table->date('tanggal_pranota');
            $table->decimal('total_gaji', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('pranota_gaji_supir_batam_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pranota_id')->constrained('pranota_gaji_supir_batams')->cascadeOnDelete();
            $table->foreignId('gaji_supir_batam_id')->unique()->constrained('gaji_supir_batams')->restrictOnDelete();
            $table->decimal('total_gaji', 15, 2);
            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('pranota_gaji_supir_batam_items');
        Schema::dropIfExists('pranota_gaji_supir_batams');
    }
};
