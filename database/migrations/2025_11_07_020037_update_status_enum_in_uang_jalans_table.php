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
        // Add new enum values to status column
        DB::statement("ALTER TABLE uang_jalans MODIFY COLUMN status ENUM('belum_dibayar', 'sudah_masuk_pranota', 'lunas', 'dibatalkan') DEFAULT 'belum_dibayar'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE uang_jalans MODIFY COLUMN status ENUM('belum_dibayar', 'lunas', 'dibatalkan') DEFAULT 'belum_dibayar'");
    }
};
