<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $destinations = DB::table('gudangs')
                ->where(function ($query) {
                    $query->whereRaw('LOWER(nama_gudang) LIKE ?', ['%temas%'])
                        ->orWhereRaw('LOWER(nama_gudang) LIKE ?', ['%meratus%']);
                })->pluck('id');
            $timestamp = now();

            foreach ($destinations as $destinationId) {
                foreach (['20ft', '40ft'] as $size) {
                    foreach (['full', 'empty'] as $status) {
                        foreach (['non_service', 'service'] as $service) {
                            $key = [
                                'gudang_tujuan_id' => $destinationId,
                                'size_kontainer' => $size,
                                'status_kontainer' => $status,
                                'status_service' => $service,
                            ];
                            if (! DB::table('master_pricelist_ob_antar_gudang')->where($key)->exists()) {
                                DB::table('master_pricelist_ob_antar_gudang')->insert($key + [
                                    'biaya' => 325000,
                                    'keterangan' => 'Tarif OB Muat Temas / Meratus Rp325.000 per kontainer.',
                                    'created_at' => $timestamp,
                                    'updated_at' => $timestamp,
                                ]);
                            }
                        }
                    }
                }
            }
        });
    }

    public function down(): void
    {
        // Preserve operational master prices that may already be used by OB transactions.
    }
};
