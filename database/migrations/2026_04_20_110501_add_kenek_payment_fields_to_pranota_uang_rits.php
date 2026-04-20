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
        Schema::table('pranota_uang_rits', function (Blueprint $table) {
            $table->string('status_pembayaran_kenek')->default('unpaid')->after('status');
            $table->date('tanggal_bayar_kenek')->nullable()->after('tanggal_bayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_uang_rits', function (Blueprint $table) {
            $table->dropColumn(['status_pembayaran_kenek', 'tanggal_bayar_kenek']);
        });
    }
};
