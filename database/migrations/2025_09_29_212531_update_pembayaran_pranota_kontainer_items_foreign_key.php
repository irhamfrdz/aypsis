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
        // Drop existing foreign key if it exists (using raw SQL for safety)
        $foreignKeyName = 'pembayaran_pranota_kontainer_items_pranota_id_foreign';
        $checkForeignKey = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'pembayaran_pranota_kontainer_items'
            AND COLUMN_NAME = 'pranota_id'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        if (!empty($checkForeignKey)) {
            $actualForeignKeyName = $checkForeignKey[0]->CONSTRAINT_NAME;
            DB::statement("ALTER TABLE pembayaran_pranota_kontainer_items DROP FOREIGN KEY `{$actualForeignKeyName}`");
        }

        // Drop unique constraint if it exists
        $checkUnique = DB::select("
            SHOW INDEX FROM pembayaran_pranota_kontainer_items
            WHERE Column_name = 'pranota_id' AND Non_unique = 0
        ");

        if (!empty($checkUnique)) {
            DB::statement("ALTER TABLE pembayaran_pranota_kontainer_items DROP INDEX pranota_id");
        }

        // Clean up invalid data before adding foreign key constraint
        DB::table('pembayaran_pranota_kontainer_items')
            ->whereNotIn('pranota_id', DB::table('pranota_tagihan_kontainer_sewa')->pluck('id'))
            ->delete();

        Schema::table('pembayaran_pranota_kontainer_items', function (Blueprint $table) {
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
