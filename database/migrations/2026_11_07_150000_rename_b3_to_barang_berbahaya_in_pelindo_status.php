<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pricelist_pelindos', function (Blueprint $table) {
            $table->enum('status_kontainer', ['empty', 'full', 'cargo', 'b3', 'barang_berbahaya', 'lpg', 'besi_beton'])->nullable()->change();
        });

        DB::table('pricelist_pelindos')
            ->where('status_kontainer', 'b3')
            ->update(['status_kontainer' => 'barang_berbahaya']);

        Schema::table('pricelist_pelindos', function (Blueprint $table) {
            $table->enum('status_kontainer', ['empty', 'full', 'cargo', 'barang_berbahaya', 'lpg', 'besi_beton'])->nullable()->change();
        });

        if (Schema::hasTable('tagihan_pelindo_items')) {
            DB::table('tagihan_pelindo_items')
                ->where('status_kontainer', 'B3')
                ->update(['status_kontainer' => 'Barang Berbahaya']);
        }
    }

    public function down(): void
    {
        Schema::table('pricelist_pelindos', function (Blueprint $table) {
            $table->enum('status_kontainer', ['empty', 'full', 'cargo', 'b3', 'barang_berbahaya', 'lpg', 'besi_beton'])->nullable()->change();
        });

        DB::table('pricelist_pelindos')
            ->where('status_kontainer', 'barang_berbahaya')
            ->update(['status_kontainer' => 'b3']);

        Schema::table('pricelist_pelindos', function (Blueprint $table) {
            $table->enum('status_kontainer', ['empty', 'full', 'cargo', 'b3', 'lpg', 'besi_beton'])->nullable()->change();
        });

        if (Schema::hasTable('tagihan_pelindo_items')) {
            DB::table('tagihan_pelindo_items')
                ->where('status_kontainer', 'Barang Berbahaya')
                ->update(['status_kontainer' => 'B3']);
        }
    }
};
