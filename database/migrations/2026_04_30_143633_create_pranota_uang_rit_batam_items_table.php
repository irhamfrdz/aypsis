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
        Schema::create('pranota_uang_rit_batam_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pranota_uang_rit_batam_id');
            $table->unsignedBigInteger('surat_jalan_batam_id');
            $table->decimal('uang_rit', 15, 2)->default(0);
            $table->timestamps();

            // Foreign keys
            $table->foreign('pranota_uang_rit_batam_id', 'fk_pur_batam_id')->references('id')->on('pranota_uang_rit_batams')->onDelete('cascade');
            $table->foreign('surat_jalan_batam_id', 'fk_sj_batam_id')->references('id')->on('surat_jalan_batams')->onDelete('cascade');

            // Unique constraint
            $table->unique(['pranota_uang_rit_batam_id', 'surat_jalan_batam_id'], 'unique_pranota_urb');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_uang_rit_batam_items');
    }
};
