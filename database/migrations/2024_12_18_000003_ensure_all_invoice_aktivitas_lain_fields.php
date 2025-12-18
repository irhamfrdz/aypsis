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
        Schema::table('invoice_aktivitas_lain', function (Blueprint $table) {
            // Check and add fields if they don't exist
            if (!Schema::hasColumn('invoice_aktivitas_lain', 'nomor_voyage')) {
                $table->string('nomor_voyage')->nullable()->after('nomor_polisi');
            }
            
            if (!Schema::hasColumn('invoice_aktivitas_lain', 'surat_jalan_id')) {
                $table->unsignedBigInteger('surat_jalan_id')->nullable()->after('nomor_voyage');
            }
            
            if (!Schema::hasColumn('invoice_aktivitas_lain', 'jenis_penyesuaian')) {
                $table->string('jenis_penyesuaian')->nullable()->after('surat_jalan_id');
            }
            
            if (!Schema::hasColumn('invoice_aktivitas_lain', 'tipe_penyesuaian')) {
                $table->json('tipe_penyesuaian')->nullable()->after('jenis_penyesuaian');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_aktivitas_lain', function (Blueprint $table) {
            $columns = ['nomor_voyage', 'surat_jalan_id', 'jenis_penyesuaian', 'tipe_penyesuaian'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('invoice_aktivitas_lain', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
