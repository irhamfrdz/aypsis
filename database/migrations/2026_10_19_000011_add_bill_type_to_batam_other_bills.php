<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan_lain_lain_batams', function (Blueprint $table) {
            $table->string('jenis_tagihan', 20)->default('kendaraan')->after('nomor_tagihan');
            $table->string('nomor_polisi', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tagihan_lain_lain_batams', function (Blueprint $table) {
            $table->dropColumn('jenis_tagihan');
            // Keep nomor_polisi nullable so existing non-vehicle bill records remain intact.
        });
    }
};
