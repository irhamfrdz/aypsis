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
        Schema::table('pranota_lembur_karyawans', function (Blueprint $table) {
            if (!Schema::hasColumn('pranota_lembur_karyawans', 'pranota_lembur_karyawan_header_id')) {
                // Also check if the old column exists and rename it if so, otherwise add new
                if (Schema::hasColumn('pranota_lembur_karyawans', 'pranota_lembur_id')) {
                    $table->renameColumn('pranota_lembur_id', 'pranota_lembur_karyawan_header_id');
                } else {
                    $table->unsignedBigInteger('pranota_lembur_karyawan_header_id')->after('id')->nullable();
                }
            }
            if (!Schema::hasColumn('pranota_lembur_karyawans', 'karyawan_id')) {
                $table->unsignedBigInteger('karyawan_id')->nullable();
            }
            if (!Schema::hasColumn('pranota_lembur_karyawans', 'jam_lembur')) {
                $table->string('jam_lembur')->nullable();
            }
            if (!Schema::hasColumn('pranota_lembur_karyawans', 'nominal_awal')) {
                $table->decimal('nominal_awal', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('pranota_lembur_karyawans', 'adjustment')) {
                $table->decimal('adjustment', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('pranota_lembur_karyawans', 'total_akhir')) {
                $table->decimal('total_akhir', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('pranota_lembur_karyawans', 'catatan')) {
                $table->text('catatan')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down method needed as this ensures schema consistency
    }
};
