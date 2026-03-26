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
        Schema::table('order_batams', function (Blueprint $table) {
            $table->enum('f_e', ['Full', 'Empty'])->default('Full')->after('tipe_kontainer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_batams', function (Blueprint $table) {
            $table->dropColumn('f_e');
        });
    }
};
