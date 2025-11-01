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
        Schema::create('pranota_uang_kenek_summary', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pranota_uang_kenek_id');
            $table->string('kenek_nama');
            $table->integer('jumlah_surat_jalan')->default(0);
            $table->decimal('total_uang_kenek', 15, 2)->default(0);
            $table->decimal('hutang', 15, 2)->default(0);
            $table->decimal('tabungan', 15, 2)->default(0);
            $table->decimal('grand_total_kenek', 15, 2)->default(0); // total_uang_kenek - hutang - tabungan
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('pranota_uang_kenek_id')->references('id')->on('pranota_uang_keneks')->onDelete('cascade');

            // Indexes
            $table->index('pranota_uang_kenek_id');
            $table->index('kenek_nama');
            
            // Unique constraint to prevent duplicate kenek in same pranota
            $table->unique(['pranota_uang_kenek_id', 'kenek_nama'], 'uk_pranota_kenek_nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_uang_kenek_summary');
    }
};
