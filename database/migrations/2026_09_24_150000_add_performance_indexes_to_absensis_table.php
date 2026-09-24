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
     *
     * Masing-masing dicek terlebih dahulu agar migration aman dijalankan ulang
     * (idempotent) meskipun index sudah pernah dibuat sebelumnya.
     */
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            // Composite index untuk query utama: WHERE karyawan_id = ? AND waktu BETWEEN ? AND ?
            if (!Schema::hasIndex('absensis', 'absensis_karyawan_id_waktu_index')) {
                $table->index(['karyawan_id', 'waktu'], 'absensis_karyawan_id_waktu_index');
            }

            // Index waktu saja untuk range scan tanpa filter karyawan (e.g. subquery inner)
            if (!Schema::hasIndex('absensis', 'absensis_waktu_index')) {
                $table->index('waktu', 'absensis_waktu_index');
            }

            // Index nik + waktu untuk correlated subquery AttendanceWorkDate (s.nik = a.nik AND s.waktu >=...)
            if (!Schema::hasIndex('absensis', 'absensis_nik_waktu_index')) {
                $table->index(['nik', 'waktu'], 'absensis_nik_waktu_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            if (Schema::hasIndex('absensis', 'absensis_karyawan_id_waktu_index')) {
                $table->dropIndex('absensis_karyawan_id_waktu_index');
            }
            if (Schema::hasIndex('absensis', 'absensis_waktu_index')) {
                $table->dropIndex('absensis_waktu_index');
            }
            if (Schema::hasIndex('absensis', 'absensis_nik_waktu_index')) {
                $table->dropIndex('absensis_nik_waktu_index');
            }
        });
    }
};

