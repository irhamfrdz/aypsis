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
        Schema::table('tanda_terimas', function (Blueprint $table) {
            // Add dimension fields after satuan
            $table->decimal('panjang', 8, 2)->nullable()->after('satuan'); // in cm
            $table->decimal('lebar', 8, 2)->nullable()->after('panjang'); // in cm
            $table->decimal('tinggi', 8, 2)->nullable()->after('lebar'); // in cm
            $table->decimal('meter_kubik', 10, 6)->nullable()->after('tinggi'); // calculated field
            $table->decimal('tonase', 8, 2)->nullable()->after('meter_kubik'); // in tons
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terimas', function (Blueprint $table) {
            $table->dropColumn([
                'panjang',
                'lebar',
                'tinggi',
                'meter_kubik',
                'tonase'
            ]);
        });
    }
};
