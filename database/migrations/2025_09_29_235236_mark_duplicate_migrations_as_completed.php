<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration marks that duplicate migrations have been completed.
     * All changes have already been applied by previous migrations.
     */
    public function up(): void
    {
        // No changes needed - all duplicate migrations have been resolved
        // This migration serves as a marker that the refactoring is complete
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reverse - this is just a marker migration
    }
};
