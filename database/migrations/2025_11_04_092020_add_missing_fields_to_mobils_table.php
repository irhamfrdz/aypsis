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
        Schema::table('mobils', function (Blueprint $table) {
            // Make nomor_polisi nullable and remove unique constraint if needed
            $table->string('nomor_polisi')->nullable()->change();
            
            // Add missing fields from CSV import
            $table->string('pemakai')->nullable()->after('atas_nama');
            $table->string('asuransi')->nullable()->after('pemakai');
            $table->date('jatuh_tempo_asuransi')->nullable()->after('asuransi');
            $table->string('warna_plat')->nullable()->after('jatuh_tempo_asuransi');
            $table->text('catatan')->nullable()->after('warna_plat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobils', function (Blueprint $table) {
            // Remove added fields
            $table->dropColumn(['pemakai', 'asuransi', 'jatuh_tempo_asuransi', 'warna_plat', 'catatan']);
            
            // Revert nomor_polisi back to not nullable (be careful with existing data)
            $table->string('nomor_polisi')->nullable(false)->change();
        });
    }
};
