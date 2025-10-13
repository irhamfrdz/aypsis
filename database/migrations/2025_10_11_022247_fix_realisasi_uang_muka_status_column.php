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
        // FIRST: Update existing data to use new status values
        DB::statement("UPDATE realisasi_uang_muka SET status = 'pending' WHERE status = 'dp_belum_terpakai'");
        DB::statement("UPDATE realisasi_uang_muka SET status = 'approved' WHERE status = 'dp_terpakai'");

        // THEN: Change status column from old ENUM values to new ones
        Schema::table('realisasi_uang_muka', function (Blueprint $table) {
            // Old: dp_belum_terpakai, dp_terpakai
            // New: pending, approved, rejected
            DB::statement("ALTER TABLE realisasi_uang_muka MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert data first
        DB::statement("UPDATE realisasi_uang_muka SET status = 'dp_belum_terpakai' WHERE status = 'pending'");
        DB::statement("UPDATE realisasi_uang_muka SET status = 'dp_terpakai' WHERE status = 'approved'");

        Schema::table('realisasi_uang_muka', function (Blueprint $table) {
            // Revert back to old ENUM values
            DB::statement("ALTER TABLE realisasi_uang_muka MODIFY COLUMN status ENUM('dp_belum_terpakai', 'dp_terpakai') DEFAULT 'dp_belum_terpakai'");
        });
    }
};
