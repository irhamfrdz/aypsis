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
        // No changes needed - original enum values are already correct for the format used in create form
        // ['20ft', '40ft', '40hc', '45ft'] matches the values used in create-lcl.blade.php
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No changes to rollback
    }
};
