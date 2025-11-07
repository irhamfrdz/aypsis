<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration removes unused fields from uang_jalans table after form simplification:
     * - bank_kas: Bank/Kas selection field 
     * - nomor_kas_bank: Auto-generated kas/bank number
     * - tanggal_kas_bank: Kas/Bank date
     * - tanggal_pemberian: Date of giving
     * - jenis_transaksi: Transaction type (debit/kredit)
     */
    public function up(): void
    {
        // Drop indexes using raw SQL with IF EXISTS check for MySQL
        try {
            \DB::statement('ALTER TABLE uang_jalans DROP INDEX IF EXISTS uang_jalans_tanggal_pemberian_index');
        } catch (\Exception $e) {
            // Continue if index doesn't exist
        }

        try {
            \DB::statement('ALTER TABLE uang_jalans DROP INDEX IF EXISTS tanggal_pemberian');
        } catch (\Exception $e) {
            // Continue if index doesn't exist
        }

        Schema::table('uang_jalans', function (Blueprint $table) {
            // Drop columns that are no longer needed after form simplification
            $columns = ['bank_kas', 'nomor_kas_bank', 'tanggal_kas_bank', 'tanggal_pemberian', 'jenis_transaksi'];
            
            foreach ($columns as $column) {
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
            $table->string('bank_kas', 255)->nullable()->after('nomor_kas_bank');
            $table->date('tanggal_pemberian')->after('keterangan'); 
            $table->enum('jenis_transaksi', ['debit', 'kredit'])->nullable()->after('kegiatan_bongkar_muat');
            
            // Restore indexes
            $table->index('tanggal_pemberian');
        });
    }
};