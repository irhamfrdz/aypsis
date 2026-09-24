<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambah index performa pada tabel absensis untuk mempercepat query:
     *  - Perhitungan lembur (filter by karyawan_id + range waktu)
     *  - AttendanceWorkDate correlated subquery (lookup by karyawan_id/nik + waktu)
     *  - Halaman absensi (filter by waktu range)
     */
    public function up(): void
    {
        $indexes = [
            'absensis_karyawan_id_waktu_index' => "ALTER TABLE `absensis` ADD INDEX `absensis_karyawan_id_waktu_index` (`karyawan_id`, `waktu`)",
            'absensis_waktu_index' => "ALTER TABLE `absensis` ADD INDEX `absensis_waktu_index` (`waktu`)",
            'absensis_nik_waktu_index' => "ALTER TABLE `absensis` ADD INDEX `absensis_nik_waktu_index` (`nik`, `waktu`)",
        ];

        foreach ($indexes as $name => $sql) {
            try {
                if (!Schema::hasIndex('absensis', $name)) {
                    DB::statement($sql);
                }
            } catch (\Throwable $e) {
                // Abaikan jika index sudah ada / duplicate
            }
        }
    }

    public function down(): void
    {
        $indexes = [
            'absensis_nik_waktu_index',
            'absensis_waktu_index',
            'absensis_karyawan_id_waktu_index',
        ];

        foreach ($indexes as $name) {
            try {
                if (Schema::hasIndex('absensis', $name)) {
                    DB::statement("ALTER TABLE `absensis` DROP INDEX `{$name}`");
                }
            } catch (\Throwable $e) {
                // Abaikan jika index tidak bisa di-drop karena dibutuhkan oleh foreign key (MySQL error 1553)
            }
        }
    }
};

