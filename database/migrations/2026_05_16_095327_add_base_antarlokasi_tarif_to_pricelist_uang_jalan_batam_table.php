<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pricelist_uang_jalan_batam', function (Blueprint $table) {
            $table->decimal('tarif_antarlokasi_20ft_base', 15, 2)->nullable()->after('tarif_antarlokasi_20ft');
            $table->decimal('tarif_antarlokasi_40ft_base', 15, 2)->nullable()->after('tarif_antarlokasi_40ft');
        });

        // Initialize base values from current values
        DB::table('pricelist_uang_jalan_batam')->update([
            'tarif_antarlokasi_20ft_base' => DB::raw('tarif_antarlokasi_20ft'),
            'tarif_antarlokasi_40ft_base' => DB::raw('tarif_antarlokasi_40ft'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricelist_uang_jalan_batam', function (Blueprint $table) {
            $table->dropColumn(['tarif_antarlokasi_20ft_base', 'tarif_antarlokasi_40ft_base']);
        });
    }
};
