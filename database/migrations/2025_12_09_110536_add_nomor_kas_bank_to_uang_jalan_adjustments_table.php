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
        Schema::table('uang_jalan_adjustments', function (Blueprint $table) {
            $table->foreignId('nomor_kas_bank')->nullable()->constrained('akun_coa')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uang_jalan_adjustments', function (Blueprint $table) {
            $table->dropForeign(['nomor_kas_bank']);
            $table->dropColumn('nomor_kas_bank');
        });
    }
};
