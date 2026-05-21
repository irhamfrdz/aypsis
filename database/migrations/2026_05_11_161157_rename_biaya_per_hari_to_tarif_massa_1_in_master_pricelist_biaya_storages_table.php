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
        Schema::table('master_pricelist_biaya_storages', function (Blueprint $table) {
            if (Schema::hasColumn('master_pricelist_biaya_storages', 'biaya_per_hari')) {
                $table->renameColumn('biaya_per_hari', 'tarif_massa_1');
            }
        });

        Schema::table('master_pricelist_biaya_storages', function (Blueprint $table) {
            if (! Schema::hasColumn('master_pricelist_biaya_storages', 'tarif_massa_2')) {
                $table->decimal('tarif_massa_2', 15, 2)->after('tarif_massa_1')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_pricelist_biaya_storages', function (Blueprint $table) {
            if (Schema::hasColumn('master_pricelist_biaya_storages', 'tarif_massa_1')) {
                $table->renameColumn('tarif_massa_1', 'biaya_per_hari');
            }
            if (Schema::hasColumn('master_pricelist_biaya_storages', 'tarif_massa_2')) {
                $table->dropColumn('tarif_massa_2');
            }
        });
    }
};
