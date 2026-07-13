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
        Schema::table('gaji_supir_batams', function (Blueprint $table) {
            $table->boolean('is_potongan_5_persen')->default(false)->after('biaya_bensin');
            $table->decimal('nominal_potongan_5_persen', 15, 2)->default(0)->after('is_potongan_5_persen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gaji_supir_batams', function (Blueprint $table) {
            $table->dropColumn(['is_potongan_5_persen', 'nominal_potongan_5_persen']);
        });
    }
};
