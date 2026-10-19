<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_pricelist_biaya_storage_pelindos', function (Blueprint $table) {
            $table->string('status_kontainer', 10)->default('full')->after('size_kontainer');
        });

        Schema::create('master_pricelist_biaya_storage_pelindo_tarif_massas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pricelist_id')->constrained('master_pricelist_biaya_storage_pelindos')->cascadeOnDelete();
            $table->decimal('massa', 10, 2);
            $table->decimal('tarif', 15, 2);
            $table->timestamps();
            $table->unique(['pricelist_id', 'massa']);
        });

        foreach (DB::table('master_pricelist_biaya_storage_pelindos')->get() as $pricelist) {
            $now = now();
            $rows = [[
                'pricelist_id' => $pricelist->id,
                'massa' => 1,
                'tarif' => $pricelist->tarif_massa_1,
                'created_at' => $now,
                'updated_at' => $now,
            ]];

            if ($pricelist->tarif_massa_2 !== null) {
                $rows[] = [
                    'pricelist_id' => $pricelist->id,
                    'massa' => 2,
                    'tarif' => $pricelist->tarif_massa_2,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('master_pricelist_biaya_storage_pelindo_tarif_massas')->insert($rows);
        }

        Schema::table('master_pricelist_biaya_storage_pelindos', function (Blueprint $table) {
            $table->dropColumn(['tarif_massa_1', 'tarif_massa_2']);
        });
    }

    public function down(): void
    {
        Schema::table('master_pricelist_biaya_storage_pelindos', function (Blueprint $table) {
            $table->decimal('tarif_massa_1', 15, 2)->default(0);
            $table->decimal('tarif_massa_2', 15, 2)->nullable();
        });

        foreach (DB::table('master_pricelist_biaya_storage_pelindos')->get() as $pricelist) {
            $rates = DB::table('master_pricelist_biaya_storage_pelindo_tarif_massas')
                ->where('pricelist_id', $pricelist->id)
                ->orderBy('massa')
                ->limit(2)
                ->pluck('tarif');

            DB::table('master_pricelist_biaya_storage_pelindos')->where('id', $pricelist->id)->update([
                'tarif_massa_1' => $rates[0] ?? 0,
                'tarif_massa_2' => $rates[1] ?? null,
            ]);
        }

        Schema::dropIfExists('master_pricelist_biaya_storage_pelindo_tarif_massas');
        Schema::table('master_pricelist_biaya_storage_pelindos', function (Blueprint $table) {
            $table->dropColumn('status_kontainer');
        });
    }
};
