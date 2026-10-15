<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran_pranota_uang_jalan_items', function (Blueprint $table) {
            $table->string('nomor_accurate')->nullable()->after('subtotal');
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran_pranota_uang_jalan_items', function (Blueprint $table) {
            $table->dropColumn('nomor_accurate');
        });
    }
};
