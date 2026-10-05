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
        Schema::table('assets', function (Blueprint $table) {
            if (Schema::hasColumn('assets', 'karyawan_id')) {
                // Drop foreign key first
                $table->dropForeign(['karyawan_id']);
                $table->dropColumn('karyawan_id');
            }

            $columnsToDrop = [];
            foreach (['nilai_perolehan', 'nilai_buku', 'lokasi', 'penanggung_jawab'] as $col) {
                if (Schema::hasColumn('assets', $col)) {
                    $columnsToDrop[] = $col;
                }
            }

            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('lokasi', 150)->nullable()->index();
            $table->decimal('nilai_perolehan', 15, 2)->default(0);
            $table->decimal('nilai_buku', 15, 2)->default(0);
            $table->string('penanggung_jawab', 150)->nullable();
            $table->foreignId('karyawan_id')->nullable()->constrained('karyawans')->nullOnDelete();
        });
    }
};
