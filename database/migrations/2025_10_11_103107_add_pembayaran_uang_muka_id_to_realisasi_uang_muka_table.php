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
        Schema::table('realisasi_uang_muka', function (Blueprint $table) {
            if (!Schema::hasColumn('realisasi_uang_muka', 'pembayaran_uang_muka_id')) {
                $table->foreignId('pembayaran_uang_muka_id')->nullable()->after('dp_amount')->constrained('pembayaran_uang_muka')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('realisasi_uang_muka', function (Blueprint $table) {
            if (Schema::hasColumn('realisasi_uang_muka', 'pembayaran_uang_muka_id')) {
                $table->dropForeign(['pembayaran_uang_muka_id']);
                $table->dropColumn('pembayaran_uang_muka_id');
            }
        });
    }
};
