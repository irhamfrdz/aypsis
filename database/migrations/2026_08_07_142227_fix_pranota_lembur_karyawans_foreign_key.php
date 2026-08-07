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

        Schema::table('pranota_lembur_karyawans', function (Blueprint $table) {
            // Add the new one pointing to the headers table
            $table->foreign('pranota_lembur_karyawan_header_id', 'fk_pranota_lembur_karyawan_header')
                  ->references('id')
                  ->on('pranota_lembur_karyawan_headers')
                  ->onDelete('cascade');
        });
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

        Schema::table('pranota_lembur_karyawans', function (Blueprint $table) {
            $table->foreign('pranota_lembur_karyawan_header_id', 'pranota_lembur_karyawans_pranota_lembur_id_foreign')
                  ->references('id')
                  ->on('pranota_lemburs')
                  ->onDelete('cascade');
        });
    }
};

