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
        Schema::table('tagihan_cat', function (Blueprint $table) {
            $table->decimal('estimasi_biaya', 15, 2)->nullable()->after('jumlah');
            $table->decimal('realisasi_biaya', 15, 2)->nullable()->after('estimasi_biaya');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_cat', function (Blueprint $table) {
            $table->dropColumn(['estimasi_biaya', 'realisasi_biaya']);
        });
    }
};
