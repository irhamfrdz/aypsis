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
        // Modify enum to add 'approved' status
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE `surat_jalans` MODIFY COLUMN `status` ENUM('draft','active','completed','cancelled','belum masuk checkpoint','sudah_checkpoint','fully_approved','rejected','approved') NOT NULL DEFAULT 'draft'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'approved' status from enum
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE `surat_jalans` MODIFY COLUMN `status` ENUM('draft','active','completed','cancelled','belum masuk checkpoint','sudah_checkpoint','fully_approved','rejected') NOT NULL DEFAULT 'draft'");
        }
    }
};
