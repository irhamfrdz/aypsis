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
        Schema::table('gaji_supir_batams', function (Blueprint $table) {
            // Create standard index first to support foreign key constraint
            $table->index('karyawan_id', 'fk_gaji_supir_karyawan_idx');

            // Drop the old biweekly unique constraint
            $table->dropUnique('unique_gaji_supir_biweekly');

            // Add custom date range columns
            $table->date('tanggal_mulai')->nullable()->after('periode_minggu');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');

            // Make monthly period columns nullable
            $table->integer('periode_bulan')->nullable()->change();
            $table->integer('periode_tahun')->nullable()->change();
            $table->integer('periode_minggu')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gaji_supir_batams', function (Blueprint $table) {
            // Re-enforce period fields as not null (with fallback defaults if needed)
            $table->integer('periode_bulan')->nullable(false)->change();
            $table->integer('periode_tahun')->nullable(false)->change();
            $table->integer('periode_minggu')->nullable(false)->change();

            // Recreate unique constraint
            $table->unique(['karyawan_id', 'periode_bulan', 'periode_tahun', 'periode_minggu'], 'unique_gaji_supir_biweekly');

            // Drop custom date range columns
            $table->dropColumn(['tanggal_mulai', 'tanggal_selesai']);

            // Drop standard index
            $table->dropIndex('fk_gaji_supir_karyawan_idx');
        });
    }
};
