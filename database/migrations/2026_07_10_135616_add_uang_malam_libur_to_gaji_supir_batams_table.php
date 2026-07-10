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
        Schema::table('gaji_supir_batams', function (Blueprint $table) {
            $table->decimal('uang_malam_libur', 15, 2)->default(0)->after('gaji_pokok');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gaji_supir_batams', function (Blueprint $table) {
            $table->dropColumn('uang_malam_libur');
        });
    }
};
