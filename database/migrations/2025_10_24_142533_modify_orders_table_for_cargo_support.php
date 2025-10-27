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
        Schema::table('orders', function (Blueprint $table) {
            // Modify size_kontainer and unit_kontainer to be nullable for cargo support
            $table->string('size_kontainer')->nullable()->change();
            $table->integer('unit_kontainer')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Revert back to not nullable
            $table->string('size_kontainer')->nullable(false)->change();
            $table->integer('unit_kontainer')->nullable(false)->change();
        });
    }
};
