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
        // Change status column from enum to string to support "Sedang Dimasak"
        try {
            Schema::table('stock_bans', function (Blueprint $table) {
                $table->string('status', 50)->default('Stok')->change();
            });
        } catch (\Exception $e) {
            // Fallback for MySQL if dbal is missing or fails
            // Modify column type to VARCHAR(50) and set default value
            DB::statement("ALTER TABLE stock_bans MODIFY status VARCHAR(50) NOT NULL DEFAULT 'Stok'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to enum if possible, but data might be lost if values don't match.
        // Safer to just keep as string or revert logic if needed, but for now we keep as string
        // or attempt to revert to enum if data is clean.
        
        // For safety, we will just change default back, but keep as string to prevent data truncation of 'Sedang Dimasak'
        try {
             Schema::table('stock_bans', function (Blueprint $table) {
                // If we want to strictly revert, we'd need to purge 'Sedang Dimasak' first
                // For now, let's just ensure it's a string with old default if needed
             });
        } catch (\Exception $e) {
            // Do nothing
        }
    }
};
