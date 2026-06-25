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
            // Add biweekly period column: 1 = Tanggal 1-15, 2 = Tanggal 16-Akhir
            $table->integer('periode_minggu')->default(1)->after('periode_tahun');

            // Add new unique constraint first to satisfy foreign key requirement
            $table->unique(['karyawan_id', 'periode_bulan', 'periode_tahun', 'periode_minggu'], 'unique_gaji_supir_biweekly');

            // Now drop old unique constraint
            $table->dropUnique('unique_gaji_supir_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gaji_supir_batams', function (Blueprint $table) {
            // Add old unique index back first
            $table->unique(['karyawan_id', 'periode_bulan', 'periode_tahun'], 'unique_gaji_supir_period');

            // Drop new unique index and column
            $table->dropUnique('unique_gaji_supir_biweekly');
            $table->dropColumn('periode_minggu');
        });
    }
};
