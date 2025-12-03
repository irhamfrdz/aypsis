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
        if (Schema::hasTable('tanda_terima_dimensi_items')) {
            Schema::table('tanda_terima_dimensi_items', function (Blueprint $table) {
                // Change decimal precision from (10,2) to (10,3) for panjang, lebar, tinggi
                // This allows for values like 2.310 instead of just 2.31
                $table->decimal('panjang', 10, 3)->nullable()->change();
                $table->decimal('lebar', 10, 3)->nullable()->change();
                $table->decimal('tinggi', 10, 3)->nullable()->change();
                $table->decimal('tonase', 10, 3)->nullable()->change();
                // meter_kubik already has (12,6) which is sufficient
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tanda_terima_dimensi_items')) {
            Schema::table('tanda_terima_dimensi_items', function (Blueprint $table) {
                // Revert back to original precision
                $table->decimal('panjang', 10, 2)->nullable()->change();
                $table->decimal('lebar', 10, 2)->nullable()->change();
                $table->decimal('tinggi', 10, 2)->nullable()->change();
                $table->decimal('tonase', 10, 2)->nullable()->change();
            });
        }
    }
};
