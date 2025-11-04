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
        Schema::table('uang_jalan_batam', function (Blueprint $table) {
            // Drop existing tanggal_berlaku column
            $table->dropColumn('tanggal_berlaku');
            
            // Add new date columns
            $table->date('tanggal_awal_berlaku')->after('status');
            $table->date('tanggal_akhir_berlaku')->after('tanggal_awal_berlaku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uang_jalan_batam', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn(['tanggal_awal_berlaku', 'tanggal_akhir_berlaku']);
            
            // Restore original column
            $table->date('tanggal_berlaku')->after('status');
        });
    }
};
