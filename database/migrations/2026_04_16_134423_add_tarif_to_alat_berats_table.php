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
        Schema::table('alat_berats', function (Blueprint $table) {
            $table->decimal('tarif_harian', 15, 2)->nullable()->after('lokasi');
            $table->decimal('tarif_bulanan', 15, 2)->nullable()->after('tarif_harian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alat_berats', function (Blueprint $table) {
            $table->dropColumn(['tarif_harian', 'tarif_bulanan']);
        });
    }
};
