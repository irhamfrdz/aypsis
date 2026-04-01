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
        Schema::table('pranota_invoice_vendor_supirs', function (Blueprint $table) {
            $table->decimal('uang_muat', 15, 2)->default(0)->after('pph');
            $table->decimal('total_uang_muat', 15, 2)->default(0)->after('uang_muat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_invoice_vendor_supirs', function (Blueprint $table) {
            $table->dropColumn(['uang_muat', 'total_uang_muat']);
        });
    }
};
