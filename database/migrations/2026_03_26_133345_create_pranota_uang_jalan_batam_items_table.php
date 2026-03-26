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
        Schema::create('pranota_uang_jalan_batam_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pranota_uang_jalan_batam_id');
            $table->unsignedBigInteger('uang_jalan_batam_id');
            $table->timestamps();

            // Indexes
            $table->index(['pranota_uang_jalan_batam_id', 'uang_jalan_batam_id'], 'pranota_ujb_items_index');
            $table->index('uang_jalan_batam_id');

            // Foreign keys
            $table->foreign('pranota_uang_jalan_batam_id', 'fk_pranota_ujb_id')->references('id')->on('pranota_uang_jalan_batams')->onDelete('cascade');
            $table->foreign('uang_jalan_batam_id', 'fk_uj_batam_id')->references('id')->on('uang_jalan_batams')->onDelete('cascade');

            // Unique constraint
            $table->unique(['pranota_uang_jalan_batam_id', 'uang_jalan_batam_id'], 'unique_pranota_ujb');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_uang_jalan_batam_items');
    }
};
