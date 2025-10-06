<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // Some DB drivers (sqlite) do not support dropping columns via ALTER TABLE.
        // When running tests with in-memory sqlite, skip the drop to avoid errors.
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            // sqlite: cannot drop columns cleanly; skip in test environment.
            return;
        }

        if (Schema::hasColumn('master_pricelist_sewa_kontainers', 'nomor_tagihan')) {
            Schema::table('master_pricelist_sewa_kontainers', function (Blueprint $table) {
                $table->dropColumn('nomor_tagihan');
            });
        }
    }

    public function down()
    {
        Schema::table('master_pricelist_sewa_kontainers', function (Blueprint $table) {
            $table->string('nomor_tagihan')->unique();
        });
    }
};
