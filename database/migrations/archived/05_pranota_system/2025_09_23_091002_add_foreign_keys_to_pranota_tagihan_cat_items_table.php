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
        Schema::table('pranota_tagihan_cat_items', function (Blueprint $table) {
            $table->foreignId('pranota_id')->constrained('pranotalist')->onDelete('cascade');
            $table->foreignId('tagihan_cat_id')->constrained('tagihan_cat')->onDelete('cascade');
            $table->unique(['pranota_id', 'tagihan_cat_id']); // Prevent duplicate relationships
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_tagihan_cat_items', function (Blueprint $table) {
            $table->dropForeign(['pranota_id']);
            $table->dropForeign(['tagihan_cat_id']);
            $table->dropUnique(['pranota_id', 'tagihan_cat_id']);
            $table->dropColumn(['pranota_id', 'tagihan_cat_id']);
        });
    }
};
