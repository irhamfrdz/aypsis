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
        $tables = ['tanda_terimas', 'tanda_terima_tanpa_surat_jalan', 'tanda_terimas_lcl'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'dokumen_ppbj')) {
                    $table->json('dokumen_ppbj')->nullable()->after('asuransi_path');
                }
                if (!Schema::hasColumn($tableName, 'dokumen_packing_list')) {
                    $table->json('dokumen_packing_list')->nullable()->after('dokumen_ppbj');
                }
                if (!Schema::hasColumn($tableName, 'dokumen_invoice')) {
                    $table->json('dokumen_invoice')->nullable()->after('dokumen_packing_list');
                }
                if (!Schema::hasColumn($tableName, 'dokumen_faktur_pajak')) {
                    $table->json('dokumen_faktur_pajak')->nullable()->after('dokumen_invoice');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['tanda_terimas', 'tanda_terima_tanpa_surat_jalan', 'tanda_terimas_lcl'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn([
                    'dokumen_ppbj',
                    'dokumen_packing_list',
                    'dokumen_invoice',
                    'dokumen_faktur_pajak',
                ]);
            });
        }
    }
};
