<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawan_apd_transactions', function (Blueprint $table) {
            $table->boolean('stock_deducted')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('karyawan_apd_transactions', function (Blueprint $table) {
            $table->dropColumn('stock_deducted');
        });
    }
};
