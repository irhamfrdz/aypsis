<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const WAREHOUSES = [
        'KANTOR AYP BATAM' => ['Gudang Kantor Srimas', 'Gudang Kontainer Bengkel Batam'],
        'KANTOR AYP JAKARTA' => ['Gudang Garasi Pluit', 'Gudang Ruko 10', 'Gudang Kantor AYP Jakarta', 'Gudang Pelabuhan', 'Dock Gudang Garasi Semut'],
    ];

    public function up(): void
    {
        Schema::table('stock_amprahans', function (Blueprint $table) {
            $table->string('lokasi_penyimpanan')->nullable()->after('lokasi');
            $table->string('gudang')->nullable()->after('lokasi_penyimpanan');
        });

        foreach (self::WAREHOUSES as $city => $warehouses) {
            DB::table('stock_amprahans')->whereIn('lokasi', $warehouses)->get(['id', 'lokasi'])
                ->each(fn ($stock) => DB::table('stock_amprahans')->where('id', $stock->id)->update([
                    'lokasi_penyimpanan' => $city,
                    'gudang' => $stock->lokasi,
                    'lokasi' => $city,
                ]));
            DB::table('stock_amprahans')->where('lokasi', $city)->whereNull('lokasi_penyimpanan')
                ->update(['lokasi_penyimpanan' => $city]);
        }
    }

    public function down(): void
    {
        DB::table('stock_amprahans')->whereNotNull('gudang')->get(['id', 'gudang'])
            ->each(fn ($stock) => DB::table('stock_amprahans')->where('id', $stock->id)
                ->update(['lokasi' => $stock->gudang]));

        Schema::table('stock_amprahans', function (Blueprint $table) {
            $table->dropColumn(['lokasi_penyimpanan', 'gudang']);
        });
    }
};
