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
        // First, update existing records to new status
        DB::table('pranotalist')->whereIn('status', ['draft', 'sent'])->update(['status' => 'unpaid']);

        // Then change the enum definition
        Schema::table('pranotalist', function (Blueprint $table) {
            // Simplify status to only 2 options: 'paid' and 'unpaid'
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid')->change();
        });
    }    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranotalist', function (Blueprint $table) {
            // Revert back to original status options
            $table->enum('status', ['draft', 'sent', 'paid', 'cancelled'])->default('draft')->change();
        });

        // Revert existing records
        DB::table('pranotalist')->where('status', 'unpaid')->update(['status' => 'draft']);
    }
};
