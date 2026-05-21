<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran_pranota_supir', function (Blueprint $table) {
            // Add columns expected by controller and tests
            $table->string('nomor_pembayaran')->nullable()->after('id');
            $table->integer('nomor_cetakan')->nullable()->after('nomor_pembayaran');
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('pembayaran_pranota_supir', function (Blueprint $table) {
            $table->dropColumn(['nomor_pembayaran', 'nomor_cetakan']);
        });
    }
};
