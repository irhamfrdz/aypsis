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
            if (!Schema::hasColumn('uang_jalans', 'bank_kas')) {
                $table->string('bank_kas', 255)->nullable()->after('nomor_kas_bank');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uang_jalans', function (Blueprint $table) {
            $table->dropColumn('bank_kas');
        });
    }
};
