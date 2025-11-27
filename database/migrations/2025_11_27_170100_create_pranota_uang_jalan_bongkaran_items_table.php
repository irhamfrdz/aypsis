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
        if (!Schema::hasTable('pranota_uang_jalan_bongkaran_items')) {
            Schema::create('pranota_uang_jalan_bongkaran_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pranota_uang_jalan_bongkaran_id');
            $table->unsignedBigInteger('uang_jalan_bongkaran_id');
            $table->timestamps();

            // Indexes
            $table->index(['pranota_uang_jalan_bongkaran_id', 'uang_jalan_bongkaran_id'], 'pranota_uang_jalan_bongkaran_items_index');
            $table->index('uang_jalan_bongkaran_id');

            // Foreign keys
            // custom short foreign key names to avoid MySQL identifier length limits
            $table->foreign('pranota_uang_jalan_bongkaran_id', 'fk_pujb_items_pranota')
                ->references('id')
                ->on('pranota_uang_jalan_bongkarans')
                ->onDelete('cascade');
            $table->foreign('uang_jalan_bongkaran_id', 'fk_pujb_items_uang_jalan')
                ->references('id')
                ->on('uang_jalan_bongkarans')
                ->onDelete('cascade');

            // Unique constraint to prevent duplicates
            $table->unique(['pranota_uang_jalan_bongkaran_id', 'uang_jalan_bongkaran_id'], 'unique_pranota_uang_jalan_bongkaran');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_uang_jalan_bongkaran_items');
    }
};
