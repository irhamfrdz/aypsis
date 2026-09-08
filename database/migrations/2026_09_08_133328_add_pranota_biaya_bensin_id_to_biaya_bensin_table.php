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
        Schema::table('biaya_bensin', function (Blueprint $table) {
            $table->foreignId('pranota_biaya_bensin_id')->nullable()->constrained('pranota_biaya_bensins')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_bensin', function (Blueprint $table) {
            $table->dropForeign(['pranota_biaya_bensin_id']);
            $table->dropColumn('pranota_biaya_bensin_id');
        });
    }
};
