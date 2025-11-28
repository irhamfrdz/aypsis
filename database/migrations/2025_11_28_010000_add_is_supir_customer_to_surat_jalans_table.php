<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddIsSupirCustomerToSuratJalansTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('surat_jalans', 'is_supir_customer')) {
            Schema::table('surat_jalans', function (Blueprint $table) {
                $table->boolean('is_supir_customer')->default(false)->after('tanggal_checkpoint');
            });
        }

        // Backfill existing records: mark as customer if 'supir' doesn't map to any karyawan
        // This is a best-effort heuristic and can be refined.
        try {
            DB::statement(
                "UPDATE surat_jalans sj
                 LEFT JOIN karyawans k ON (sj.supir = k.nama_panggilan OR sj.supir = k.nama_lengkap)
                 SET sj.is_supir_customer = 1
                 WHERE k.id IS NULL AND sj.supir IS NOT NULL AND TRIM(sj.supir) <> ''"
            );
        } catch (\Exception $e) {
            // Ignore errors: if backfill fails due to SQL differences we'll skip it
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('surat_jalans', 'is_supir_customer')) {
            Schema::table('surat_jalans', function (Blueprint $table) {
                $table->dropColumn('is_supir_customer');
            });
        }
    }
}
