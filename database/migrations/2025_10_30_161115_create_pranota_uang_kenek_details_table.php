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
        Schema::create('pranota_uang_kenek_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pranota_uang_kenek_id');
            $table->unsignedBigInteger('surat_jalan_id');
            $table->string('no_surat_jalan');
            $table->string('supir_nama')->nullable();
            $table->string('kenek_nama');
            $table->string('no_plat');
            $table->decimal('uang_rit_kenek', 15, 2)->default(50000);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('pranota_uang_kenek_id')->references('id')->on('pranota_uang_keneks')->onDelete('cascade');
            $table->foreign('surat_jalan_id')->references('id')->on('surat_jalans')->onDelete('cascade');

            // Indexes
            $table->index('pranota_uang_kenek_id');
            $table->index('surat_jalan_id');
            $table->index('kenek_nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_uang_kenek_details');
    }
};
