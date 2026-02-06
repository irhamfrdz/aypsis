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
        Schema::table('stock_amprahans', function (Blueprint $table) {
            $table->string('nama_barang')->nullable()->after('id');
            $table->string('type_barang')->nullable()->after('nama_barang');
            $table->decimal('harga_satuan', 15, 2)->default(0)->after('type_barang');
            // Make existing column nullable
            $table->unsignedBigInteger('master_nama_barang_amprahan_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_amprahans', function (Blueprint $table) {
            $table->dropColumn(['nama_barang', 'type_barang', 'harga_satuan']);
            // Revert nullable change (might fail if data is null, but best effort)
            $table->unsignedBigInteger('master_nama_barang_amprahan_id')->nullable(false)->change();
        });
    }
};
