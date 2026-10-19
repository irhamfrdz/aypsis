<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasVendor = Schema::hasColumn('master_pricelist_biaya_storage_pelindos', 'vendor');
        $hasLocation = Schema::hasColumn('master_pricelist_biaya_storage_pelindos', 'lokasi');

        if ($hasVendor || $hasLocation) {
            Schema::table('master_pricelist_biaya_storage_pelindos', function (Blueprint $table) use ($hasVendor, $hasLocation) {
                $table->dropIndex('storage_pelindo_lookup_index');
                $columns = array_values(array_filter([
                    $hasVendor ? 'vendor' : null,
                    $hasLocation ? 'lokasi' : null,
                ]));
                $table->dropColumn($columns);
            });
        }
    }

    public function down(): void
    {
        $hasVendor = Schema::hasColumn('master_pricelist_biaya_storage_pelindos', 'vendor');
        $hasLocation = Schema::hasColumn('master_pricelist_biaya_storage_pelindos', 'lokasi');

        if (! $hasVendor || ! $hasLocation) {
            Schema::table('master_pricelist_biaya_storage_pelindos', function (Blueprint $table) use ($hasVendor, $hasLocation) {
                if (! $hasVendor) {
                    $table->string('vendor')->nullable();
                }
                if (! $hasLocation) {
                    $table->string('lokasi')->nullable();
                }
                $table->index(['vendor', 'lokasi', 'size_kontainer'], 'storage_pelindo_lookup_index');
            });
        }
    }
};
