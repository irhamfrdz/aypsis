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
        Schema::table('master_uang_lemburs', function (Blueprint $table) {
            $table->decimal('pengali_uang_makan_hari_libur', 5, 2)->default(1)->after('sub_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_uang_lemburs', function (Blueprint $table) {
            $table->dropColumn('pengali_uang_makan_hari_libur');
        });
    }
};
