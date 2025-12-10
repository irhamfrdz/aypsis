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
        // Add a new foreign key column that references pranota_tagihan_cat table. Keep pranota_id for backward compatibility
        Schema::table('pranota_tagihan_cat_items', function (Blueprint $table) {
            if (!Schema::hasColumn('pranota_tagihan_cat_items', 'pranota_tagihan_cat_id')) {
                $table->foreignId('pranota_tagihan_cat_id')->nullable()->after('pranota_id')->constrained('pranota_tagihan_cat')->onDelete('cascade');
                // Add a new unique key to avoid duplicates if needed (optional)
                $table->unique(['pranota_tagihan_cat_id', 'tagihan_cat_id'], 'pranota_tagihan_cat_items_pranota_tagihan_cat_id_tagihan_cat_id_unique');
            }
        });

        // Note: We intentionally avoid dropping the old pranota_id column to avoid data loss during transition.
        // Migration to populate new column from old pranota_id isn't included because mapping requires manual verification.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_tagihan_cat_items', function (Blueprint $table) {
            if (Schema::hasColumn('pranota_tagihan_cat_items', 'pranota_tagihan_cat_id')) {
                $table->dropForeign(['pranota_tagihan_cat_id']);
                $table->dropUnique('pranota_tagihan_cat_items_pranota_tagihan_cat_id_tagihan_cat_id_unique');
                $table->dropColumn('pranota_tagihan_cat_id');
            }
        });
    }
};
