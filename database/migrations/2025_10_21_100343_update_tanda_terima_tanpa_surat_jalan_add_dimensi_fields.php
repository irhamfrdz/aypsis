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
        Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            // Add new dimension fields
            $table->decimal('panjang', 8, 2)->nullable()->after('berat'); // in cm
            $table->decimal('lebar', 8, 2)->nullable()->after('panjang'); // in cm
            $table->decimal('tinggi', 8, 2)->nullable()->after('lebar'); // in cm
            $table->decimal('meter_kubik', 10, 6)->nullable()->after('tinggi'); // calculated field
            $table->decimal('tonase', 8, 2)->nullable()->after('meter_kubik'); // in tons

            // Remove old dimensi field if it exists (it may not exist in current structure)
            // $table->dropColumn('dimensi'); // commented out as it doesn't exist in original migration
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            // Drop the new fields
            $table->dropColumn([
                'panjang',
                'lebar',
                'tinggi',
                'meter_kubik',
                'tonase'
            ]);

            // Add back dimensi field if needed
            // $table->string('dimensi')->nullable();
        });
    }
};
