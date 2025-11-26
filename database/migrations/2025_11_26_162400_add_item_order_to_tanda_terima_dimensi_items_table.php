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
        if (Schema::hasTable('tanda_terima_dimensi_items')) {
            Schema::table('tanda_terima_dimensi_items', function (Blueprint $table) {
                if (!Schema::hasColumn('tanda_terima_dimensi_items', 'item_order')) {
                    $table->integer('item_order')->default(0)->after('tonase');
                }
            });

            // If previous migration used 'urutan', copy existing values into 'item_order'
            if (Schema::hasColumn('tanda_terima_dimensi_items', 'urutan')) {
                DB::statement('UPDATE tanda_terima_dimensi_items SET item_order = urutan WHERE urutan IS NOT NULL');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tanda_terima_dimensi_items')) {
            Schema::table('tanda_terima_dimensi_items', function (Blueprint $table) {
                if (Schema::hasColumn('tanda_terima_dimensi_items', 'item_order')) {
                    $table->dropColumn('item_order');
                }
            });
        }
    }
};
