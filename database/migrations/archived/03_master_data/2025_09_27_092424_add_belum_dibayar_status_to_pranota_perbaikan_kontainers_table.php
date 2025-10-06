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
        // Add 'belum_dibayar' to the enum status
        DB::statement("ALTER TABLE pranota_perbaikan_kontainers MODIFY COLUMN status ENUM('draft', 'belum_dibayar', 'approved', 'in_progress', 'completed', 'cancelled') DEFAULT 'belum_dibayar'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'belum_dibayar' from the enum status
        DB::statement("ALTER TABLE pranota_perbaikan_kontainers MODIFY COLUMN status ENUM('draft', 'approved', 'in_progress', 'completed', 'cancelled') DEFAULT 'draft'");
    }
};
