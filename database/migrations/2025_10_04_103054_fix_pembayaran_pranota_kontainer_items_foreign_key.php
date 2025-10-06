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
        Schema::table('pembayaran_pranota_kontainer_items', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['pranota_id']);

            // Add new foreign key to correct table
            $table->foreign('pranota_id')
                  ->references('id')
                  ->on('pranota_tagihan_kontainer_sewa')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_pranota_kontainer_items', function (Blueprint $table) {
            // Drop the current foreign key
            $table->dropForeign(['pranota_id']);

            // Restore the old foreign key (even though table doesn't exist)
            $table->foreign('pranota_id')
                  ->references('id')
                  ->on('pranotalist')
                  ->onDelete('cascade');
        });
    }
};
