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
        Schema::table('invoice_aktivitas_lain_utilities', function (Blueprint $table) {
            $table->string('vendor')->nullable()->after('alat_berat_id');
            $table->string('kode_bayar')->nullable()->after('vendor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_aktivitas_lain_utilities', function (Blueprint $table) {
            $table->dropColumn(['vendor', 'kode_bayar']);
        });
    }
};
