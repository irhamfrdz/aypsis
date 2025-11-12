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
        Schema::table('pricelist_uang_jalan_batam', function (Blueprint $table) {
            $table->dropColumn(['tanggal_awal_berlaku', 'tanggal_akhir_berlaku']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricelist_uang_jalan_batam', function (Blueprint $table) {
            $table->date('tanggal_awal_berlaku')->nullable()->after('status');
            $table->date('tanggal_akhir_berlaku')->nullable()->after('tanggal_awal_berlaku');
        });
    }
};
