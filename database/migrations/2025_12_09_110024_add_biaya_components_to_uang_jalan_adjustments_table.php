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
        Schema::table('uang_jalan_adjustments', function (Blueprint $table) {
            $table->decimal('jumlah_mel', 15, 2)->nullable()->after('jumlah_penyesuaian');
            $table->decimal('jumlah_pelancar', 15, 2)->nullable()->after('jumlah_mel');
            $table->decimal('jumlah_kawalan', 15, 2)->nullable()->after('jumlah_pelancar');
            $table->decimal('jumlah_parkir', 15, 2)->nullable()->after('jumlah_kawalan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uang_jalan_adjustments', function (Blueprint $table) {
            $table->dropColumn(['jumlah_mel', 'jumlah_pelancar', 'jumlah_kawalan', 'jumlah_parkir']);
        });
    }
};
