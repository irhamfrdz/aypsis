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
        Schema::table('vendor_kanisirs', function (Blueprint $table) {
            $table->renameColumn('nama_barang', 'ukuran');
            $table->string('tipe')->nullable()->after('harga'); // For 'benang', 'kawat'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_kanisirs', function (Blueprint $table) {
            $table->renameColumn('ukuran', 'nama_barang');
            $table->dropColumn('tipe');
        });
    }
};
