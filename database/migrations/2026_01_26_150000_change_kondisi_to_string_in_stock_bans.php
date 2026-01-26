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
        // Try to use Laravel's change method first if dbal is installed, otherwise fallback to raw SQL
        try {
            Schema::table('stock_bans', function (Blueprint $table) {
                // Change enum to string to support new values
                // We drop default first to avoid conflicts
                $table->string('kondisi', 50)->default('asli')->change();
            });
        } catch (\Exception $e) {
            // Fallback for MySQL if dbal is missing
            DB::statement("ALTER TABLE stock_bans MODIFY kondisi VARCHAR(50) NOT NULL DEFAULT 'asli'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We cannot easily revert back to enum if data contains new values not in enum
        // So we just keep it as string or revert to old default
        Schema::table('stock_bans', function (Blueprint $table) {
             // Optional: revert to 'Baru' default, but keep as string to prevent data loss
             $table->string('kondisi', 50)->default('Baru')->change();
        });
    }
};
