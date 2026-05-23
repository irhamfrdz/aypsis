<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'approved' to the status_pembayaran enum in pranota_uang_jalans table
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE pranota_uang_jalans MODIFY COLUMN status_pembayaran ENUM('unpaid', 'approved', 'paid', 'partial', 'cancelled') DEFAULT 'approved'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'approved' from the status_pembayaran enum
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE pranota_uang_jalans MODIFY COLUMN status_pembayaran ENUM('unpaid', 'paid', 'partial', 'cancelled') DEFAULT 'unpaid'");
        }
    }
};
