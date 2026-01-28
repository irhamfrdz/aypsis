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
        Schema::table('vendor_kanisirs', function (Blueprint $バランス) {
            $バランス->dropColumn(['no_telp', 'alamat']);
            $バランス->string('nama_barang')->after('nama')->nullable();
            $バランス->decimal('harga', 15, 2)->after('nama_barang')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_kanisirs', function (Blueprint $バランス) {
            $バランス->string('no_telp')->nullable();
            $バランス->text('alamat')->nullable();
            $バランス->dropColumn(['nama_barang', 'harga']);
        });
    }
};
