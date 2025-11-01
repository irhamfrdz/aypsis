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
        // Check if column exists and needs update
        try {
            // Update enum values untuk kolom status di tabel surat_jalans
            DB::statement("ALTER TABLE surat_jalans MODIFY COLUMN status ENUM('draft', 'active', 'completed', 'cancelled', 'belum masuk checkpoint', 'sudah_checkpoint', 'fully_approved', 'rejected') DEFAULT 'belum masuk checkpoint'");
        } catch (\Exception $e) {
            // If error occurs (column already updated), skip
            \Log::info('Surat jalans status column already updated or error: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback ke enum values sebelumnya
        DB::statement("ALTER TABLE surat_jalans MODIFY COLUMN status ENUM('draft', 'active', 'completed', 'cancelled', 'belum masuk checkpoint') DEFAULT 'belum masuk checkpoint'");
    }
};