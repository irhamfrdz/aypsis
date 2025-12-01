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
            $table->string('nama_barang')->nullable()->after('item_number');
            $table->integer('jumlah')->nullable()->after('nama_barang');
            $table->string('satuan', 50)->nullable()->after('jumlah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terima_lcl_items', function (Blueprint $table) {
            $table->dropColumn(['nama_barang', 'jumlah', 'satuan']);
        });
    }
};
