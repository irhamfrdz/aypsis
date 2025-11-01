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
        // Update enum values untuk kolom status di tabel surat_jalans
        DB::statement("ALTER TABLE surat_jalans MODIFY COLUMN status ENUM('draft', 'active', 'completed', 'cancelled', 'belum masuk checkpoint', 'sudah_checkpoint', 'fully_approved', 'rejected') DEFAULT 'belum masuk checkpoint'");
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