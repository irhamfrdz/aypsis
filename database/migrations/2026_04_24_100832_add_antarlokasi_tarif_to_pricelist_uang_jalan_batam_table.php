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
            $table->decimal('tarif_antarlokasi_20ft', 15, 2)->nullable()->after('tarif_40ft_empty');
            $table->decimal('tarif_antarlokasi_40ft', 15, 2)->nullable()->after('tarif_antarlokasi_20ft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricelist_uang_jalan_batam', function (Blueprint $table) {
            $table->dropColumn(['tarif_antarlokasi_20ft', 'tarif_antarlokasi_40ft']);
        });
    }
};
