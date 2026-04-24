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
        Schema::table('pricelist_uang_jalan_batam', function (Blueprint $table) {
            $table->dropColumn('f_e');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricelist_uang_jalan_batam', function (Blueprint $table) {
            $table->enum('f_e', ['Full', 'Empty'])->after('size');
        });
    }
};
