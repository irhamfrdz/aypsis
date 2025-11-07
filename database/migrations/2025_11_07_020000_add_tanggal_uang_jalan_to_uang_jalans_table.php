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
        Schema::table('uang_jalans', function (Blueprint $table) {
            // Add column only if it doesn't exist
            if (!Schema::hasColumn('uang_jalans', 'tanggal_uang_jalan')) {
                $table->date('tanggal_uang_jalan')->nullable()->after('nomor_uang_jalan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uang_jalans', function (Blueprint $table) {
            $table->dropColumn('tanggal_uang_jalan');
        });
    }
};