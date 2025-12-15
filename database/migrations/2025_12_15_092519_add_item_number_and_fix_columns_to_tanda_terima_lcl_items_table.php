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
        Schema::table('tanda_terima_lcl_items', function (Blueprint $table) {
            // Add item_number column after tanda_terima_lcl_id
            $table->integer('item_number')->default(1)->after('tanda_terima_lcl_id');
            
            // Rename jumlah_koli to jumlah untuk match dengan form
            $table->renameColumn('jumlah_koli', 'jumlah');
            
            // Add satuan column that's missing
            $table->string('satuan', 50)->nullable()->after('jumlah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terima_lcl_items', function (Blueprint $table) {
            // Remove added columns
            $table->dropColumn(['item_number', 'satuan']);
            
            // Rename back jumlah to jumlah_koli
            $table->renameColumn('jumlah', 'jumlah_koli');
        });
    }
};
