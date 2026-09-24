<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        Schema::table('absensis', function (Blueprint $table) {
            // Composite index untuk query utama: WHERE karyawan_id = ? AND waktu BETWEEN ? AND ?
            if (!$this->hasIndex('absensis', 'absensis_karyawan_id_waktu_index')) {
                $table->index(['karyawan_id', 'waktu'], 'absensis_karyawan_id_waktu_index');
            }

            // Index waktu saja untuk range scan tanpa filter karyawan (e.g. subquery inner)
            if (!$this->hasIndex('absensis', 'absensis_waktu_index')) {
                $table->index('waktu', 'absensis_waktu_index');
            }

            // Index nik + waktu untuk correlated subquery AttendanceWorkDate (s.nik = a.nik AND s.waktu >=...)
            if (!$this->hasIndex('absensis', 'absensis_nik_waktu_index')) {
                $table->index(['nik', 'waktu'], 'absensis_nik_waktu_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropIndexIfExists('absensis_karyawan_id_waktu_index');
            $table->dropIndexIfExists('absensis_waktu_index');
            $table->dropIndexIfExists('absensis_nik_waktu_index');
        });
    }

    /**
     * Helper: cek apakah index sudah ada (agar migration aman dijalankan ulang)
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $indexes = \DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }
};
