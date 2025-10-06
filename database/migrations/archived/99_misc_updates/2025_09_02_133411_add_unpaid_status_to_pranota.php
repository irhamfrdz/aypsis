<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pranotalist', function (Blueprint $table) {
            // Add 'unpaid' to existing enum temporarily
            $table->enum('status', ['draft', 'sent', 'paid', 'cancelled', 'unpaid'])->default('unpaid')->change();
        });

        // Update all non-paid status to unpaid
        DB::table('pranotalist')->whereIn('status', ['draft', 'sent', 'cancelled'])->update(['status' => 'unpaid']);

        // Now simplify to only 2 statuses
        Schema::table('pranotalist', function (Blueprint $table) {
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranotalist', function (Blueprint $table) {
            // Revert back to original status options
            $table->enum('status', ['draft', 'sent', 'paid', 'cancelled'])->default('draft')->change();
        });

        // Update unpaid back to draft
        DB::table('pranotalist')->where('status', 'unpaid')->update(['status' => 'draft']);
    }
};
