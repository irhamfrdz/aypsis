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
        Schema::create('pembayaran_pranota_perbaikan_kontainer_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_pranota_perbaikan_kontainer_id');
            $table->unsignedBigInteger('pranota_perbaikan_kontainer_id');
            $table->decimal('nominal_dibayar', 15, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Foreign keys with custom names
            $table->foreign('pembayaran_pranota_perbaikan_kontainer_id', 'fk_ppbk_items_pembayaran')
                ->references('id')
                ->on('pembayaran_pranota_perbaikan_kontainers')
                ->onDelete('cascade');

            $table->foreign('pranota_perbaikan_kontainer_id', 'fk_ppbk_items_pranota')
                ->references('id')
                ->on('pranota_perbaikan_kontainers')
                ->onDelete('cascade');

            // Prevent duplicate entries
            $table->unique(['pembayaran_pranota_perbaikan_kontainer_id', 'pranota_perbaikan_kontainer_id'], 'unique_ppbk_items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_perbaikan_kontainer_items');
    }
};
