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
        Schema::table('stock_kontainers', function (Blueprint $table) {
            // Check if column exists before dropping it
            if (Schema::hasColumn('stock_kontainers', 'nomor_seri')) {
                $table->dropColumn('nomor_seri');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_kontainers', function (Blueprint $table) {
            $table->string('nomor_seri')->nullable()->after('keterangan');
        });
    }
};
