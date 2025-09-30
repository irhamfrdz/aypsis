<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clean up invalid data before adding foreign key constraint
        DB::table('pembayaran_pranota_kontainer_items')
            ->whereNotIn('pranota_id', DB::table('pranota_tagihan_kontainer_sewa')->pluck('id'))
            ->delete();

        Schema::table('pembayaran_pranota_kontainer_items', function (Blueprint $table) {
            // Drop existing foreign key and unique constraint
            $table->dropForeign(['pranota_id']);
            $table->dropUnique(['pranota_id']);

            // Add new foreign key to pranota_tagihan_kontainer_sewa
            $table->foreign('pranota_id')->references('id')->on('pranota_tagihan_kontainer_sewa')->onDelete('cascade');

            // Ensure each pranota can only be paid once
            $table->unique('pranota_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_pranota_kontainer_items', function (Blueprint $table) {
            // Drop new foreign key and unique constraint
            $table->dropForeign(['pranota_id']);
            $table->dropUnique(['pranota_id']);

            // Restore original foreign key to pranotalist
            $table->foreign('pranota_id')->references('id')->on('pranotalist')->onDelete('cascade');

            // Ensure each pranota can only be paid once
            $table->unique('pranota_id');
        });
    }
};
