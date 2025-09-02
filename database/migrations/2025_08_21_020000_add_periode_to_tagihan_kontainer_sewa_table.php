<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'periode')) {
                $table->string('periode')->nullable()->after('tanggal_harga_awal');
            }
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
            if (Schema::hasColumn('tagihan_kontainer_sewa', 'periode')) {
                $table->dropColumn('periode');
            }
        });
    }
};
