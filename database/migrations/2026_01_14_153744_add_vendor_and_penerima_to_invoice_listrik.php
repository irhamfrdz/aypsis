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
        // Add vendor_listrik to invoice_aktivitas_lain
        Schema::table('invoice_aktivitas_lain', function (Blueprint $table) {
            $table->string('vendor_listrik')->nullable()->after('penerima');
        });
        
        // Add penerima to invoice_aktivitas_lain_listrik
        Schema::table('invoice_aktivitas_lain_listrik', function (Blueprint $table) {
            $table->string('penerima')->nullable()->after('referensi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_aktivitas_lain', function (Blueprint $table) {
            $table->dropColumn('vendor_listrik');
        });
        
        Schema::table('invoice_aktivitas_lain_listrik', function (Blueprint $table) {
            $table->dropColumn('penerima');
        });
    }
};
