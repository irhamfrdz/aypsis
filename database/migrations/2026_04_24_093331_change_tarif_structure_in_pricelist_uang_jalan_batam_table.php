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
            $table->decimal('tarif_20ft_full', 15, 2)->nullable()->after('ring');
            $table->decimal('tarif_20ft_empty', 15, 2)->nullable()->after('tarif_20ft_full');
            $table->decimal('tarif_40ft_full', 15, 2)->nullable()->after('tarif_20ft_empty');
            $table->decimal('tarif_40ft_empty', 15, 2)->nullable()->after('tarif_40ft_full');
            $table->dropColumn(['tarif', 'tarif_base']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricelist_uang_jalan_batam', function (Blueprint $table) {
            $table->decimal('tarif', 15, 2)->nullable()->after('ring');
            $table->decimal('tarif_base', 15, 2)->nullable()->after('tarif');
            $table->dropColumn(['tarif_20ft_full', 'tarif_20ft_empty', 'tarif_40ft_full', 'tarif_40ft_empty']);
        });
    }
};
