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
        // Drop old foreign key if it exists using raw DB statement to safely catch exceptions
        try {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE `pranota_lembur_karyawans` DROP FOREIGN KEY `pranota_lembur_karyawans_pranota_lembur_id_foreign`');
        } catch (\Exception $e) {
            // Ignore if it doesn't exist
        }

        // Add the new one pointing to the headers table safely
        try {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE `pranota_lembur_karyawans` ADD CONSTRAINT `fk_pranota_lembur_karyawan_header` FOREIGN KEY (`pranota_lembur_karyawan_header_id`) REFERENCES `pranota_lembur_karyawan_headers` (`id`) ON DELETE CASCADE');
        } catch (\Exception $e) {
            // Ignore if it already exists or if column doesn't exist yet
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE `pranota_lembur_karyawans` DROP FOREIGN KEY `fk_pranota_lembur_karyawan_header`');
        } catch (\Exception $e) {
            // Ignore if it doesn't exist
        }

        try {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE `pranota_lembur_karyawans` ADD CONSTRAINT `pranota_lembur_karyawans_pranota_lembur_id_foreign` FOREIGN KEY (`pranota_lembur_karyawan_header_id`) REFERENCES `pranota_lemburs` (`id`) ON DELETE CASCADE');
        } catch (\Exception $e) {
            // Ignore if it already exists or if column doesn't exist
        }
    }
};

