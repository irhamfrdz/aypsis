<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran_pranota_bpjs', function (Blueprint $table) {
            $table->json('bank_allocations')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran_pranota_bpjs', function (Blueprint $table) {
            $table->dropColumn('bank_allocations');
        });
    }
};
