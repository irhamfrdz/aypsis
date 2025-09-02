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
        if (Schema::hasColumn('tagihan_kontainer_sewa', 'periode_raw')) {
            Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
                $table->dropColumn('periode_raw');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('tagihan_kontainer_sewa', 'periode_raw')) {
            Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
                $table->string('periode_raw')->nullable()->after('tanggal_harga_awal');
            });
        }
    }
};
