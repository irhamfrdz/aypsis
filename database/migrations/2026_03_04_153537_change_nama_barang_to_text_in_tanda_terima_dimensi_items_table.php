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
        Schema::table('tanda_terima_dimensi_items', function (Blueprint $table) {
            $table->text('nama_barang')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terima_dimensi_items', function (Blueprint $table) {
            $table->string('nama_barang', 255)->nullable()->change();
        });
    }
};
