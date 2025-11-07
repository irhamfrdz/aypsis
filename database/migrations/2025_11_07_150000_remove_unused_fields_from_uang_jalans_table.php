<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration removes unused fields from uang_jalans table after form simplification:
     * - bank_kas: Bank/Kas selection field (from original table)
     * - nomor_kas_bank: Auto-generated kas/bank number
     * - tanggal_kas_bank: Kas/Bank date
     * - tanggal_pemberian: Date of giving (from original table)
     * - jenis_transaksi: Transaction type (debit/kredit)
     */
    public function up(): void
    {
        // Drop indexes first using raw SQL with proper error handling
        $indexesToDrop = [
            'uang_jalans_tanggal_pemberian_index',
            'uang_jalans_status_tanggal_pemberian_index',
            'tanggal_pemberian'
        ];

        foreach ($indexesToDrop as $indexName) {
            try {
                DB::statement("DROP INDEX IF EXISTS `{$indexName}` ON `uang_jalans`");
            } catch (\Exception $e) {
                // Continue if index doesn't exist
            }
        }

        Schema::table('uang_jalans', function (Blueprint $table) {
            // Drop columns that are no longer needed after form simplification
            $columnsToRemove = [
                'bank_kas',
                'nomor_kas_bank', 
                'tanggal_kas_bank',
                'tanggal_pemberian',
                'jenis_transaksi'
            ];
            
            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('uang_jalans', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uang_jalans', function (Blueprint $table) {
            // Restore the dropped columns with their original definitions
            $table->string('nomor_kas_bank', 50)->nullable()->after('nomor_uang_jalan');
            $table->date('tanggal_kas_bank')->nullable()->after('nomor_kas_bank');
            $table->enum('bank_kas', ['bank', 'kas'])->default('kas')->after('tanggal_kas_bank');
            $table->date('tanggal_pemberian')->after('status'); 
            $table->enum('jenis_transaksi', ['debit', 'kredit'])->nullable()->after('tanggal_pemberian');
            
            // Restore indexes
            $table->index(['status', 'tanggal_pemberian']);
        });
    }
};