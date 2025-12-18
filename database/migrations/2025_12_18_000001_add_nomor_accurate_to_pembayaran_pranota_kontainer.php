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
        Schema::table('pembayaran_pranota_kontainer', function (Blueprint $table) {
            $table->string('nomor_accurate')->nullable()->after('nomor_pembayaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_pranota_kontainer', function (Blueprint $table) {
            $table->dropColumn('nomor_accurate');
        });
    }
};
