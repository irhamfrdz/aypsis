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
            $table->enum('status_kontainer', [
                'empty', 'full', 'cargo', 'barang_berbahaya', 'lpg', 'besi_beton', 'mobil', 'alat_berat', 'palka',
            ])->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('pricelist_pelindos')
            ->where('status_kontainer', 'palka')
            ->update(['status_kontainer' => null]);

        Schema::table('pricelist_pelindos', function (Blueprint $table) {
            $table->enum('status_kontainer', [
                'empty', 'full', 'cargo', 'barang_berbahaya', 'lpg', 'besi_beton', 'mobil', 'alat_berat',
            ])->nullable()->change();
        });
    }
};
