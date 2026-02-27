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
        $tables = [
            'tanda_terimas',
            'tanda_terima_tanpa_surat_jalan',
            'tanda_terimas_lcl'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                // Dokumen Asuransi
                $table->string('asuransi_path')->nullable();
                $table->timestamp('asuransi_uploaded_at')->nullable();
                $table->foreignId('asuransi_uploaded_by')->nullable()->constrained('users')->onDelete('set null');
                
                // Approval
                $table->boolean('is_asuransi_approved')->default(false);
                $table->timestamp('asuransi_approved_at')->nullable();
                $table->foreignId('asuransi_approved_by')->nullable()->constrained('users')->onDelete('set null');
                
                // Keterangan Asuransi (Opsional)
                $table->text('asuransi_keterangan')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'tanda_terimas',
            'tanda_terima_tanpa_surat_jalan',
            'tanda_terimas_lcl'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['asuransi_uploaded_by']);
                $table->dropForeign(['asuransi_approved_by']);
                $table->dropColumn([
                    'asuransi_path',
                    'asuransi_uploaded_at',
                    'asuransi_uploaded_by',
                    'is_asuransi_approved',
                    'asuransi_approved_at',
                    'asuransi_approved_by',
                    'asuransi_keterangan'
                ]);
            });
        }
    }
};
