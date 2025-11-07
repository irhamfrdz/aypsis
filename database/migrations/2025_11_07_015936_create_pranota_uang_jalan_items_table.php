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
        Schema::create('pranota_uang_jalan_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pranota_uang_jalan_id');
            $table->unsignedBigInteger('uang_jalan_id');
            $table->timestamps();

            // Indexes
            $table->index(['pranota_uang_jalan_id', 'uang_jalan_id'], 'pranota_uang_jalan_items_index');
            $table->index('uang_jalan_id');

            // Foreign keys
            $table->foreign('pranota_uang_jalan_id')->references('id')->on('pranota_uang_jalans')->onDelete('cascade');
            $table->foreign('uang_jalan_id')->references('id')->on('uang_jalans')->onDelete('cascade');

            // Unique constraint untuk mencegah duplikasi
            $table->unique(['pranota_uang_jalan_id', 'uang_jalan_id'], 'unique_pranota_uang_jalan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_uang_jalan_items');
    }
};
