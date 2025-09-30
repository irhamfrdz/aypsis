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
        Schema::table('pranota_tagihan_cat', function (Blueprint $table) {
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_tagihan_cat', function (Blueprint $table) {
            $table->enum('status', ['unpaid', 'paid', 'cancelled'])->default('unpaid')->change();
        });
    }
};
