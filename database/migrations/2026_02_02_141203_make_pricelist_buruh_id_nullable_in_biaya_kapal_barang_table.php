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
        Schema::table('biaya_kapal_barang', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['pricelist_buruh_id']);
            
            // Make column nullable
            $table->unsignedBigInteger('pricelist_buruh_id')->nullable()->change();
            
            // Re-add foreign key with nullable
            $table->foreign('pricelist_buruh_id')->references('id')->on('pricelist_buruh')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_barang', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['pricelist_buruh_id']);
            
            // Make column not nullable
            $table->unsignedBigInteger('pricelist_buruh_id')->nullable(false)->change();
            
            // Re-add foreign key
            $table->foreign('pricelist_buruh_id')->references('id')->on('pricelist_buruh')->onDelete('restrict');
        });
    }
};
