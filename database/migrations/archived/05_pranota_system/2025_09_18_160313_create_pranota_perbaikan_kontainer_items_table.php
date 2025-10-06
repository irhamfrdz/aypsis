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
        Schema::create('pranota_perbaikan_kontainer_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pranota_perbaikan_kontainer_id');
            $table->unsignedBigInteger('perbaikan_kontainer_id');
            $table->decimal('biaya_item', 15, 2)->nullable(); // Biaya untuk item spesifik ini
            $table->text('catatan_item')->nullable(); // Catatan khusus untuk item ini
            $table->timestamps();

            // Foreign keys with custom names
            $table->foreign('pranota_perbaikan_kontainer_id', 'fk_pranota_items_pranota')
                  ->references('id')->on('pranota_perbaikan_kontainers')
                  ->onDelete('cascade');
            $table->foreign('perbaikan_kontainer_id', 'fk_pranota_items_perbaikan')
                  ->references('id')->on('perbaikan_kontainers')
                  ->onDelete('cascade');

            // Unique constraint with custom name
            $table->unique(['pranota_perbaikan_kontainer_id', 'perbaikan_kontainer_id'], 'unique_pranota_perbaikan_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_perbaikan_kontainer_items');
    }
};
