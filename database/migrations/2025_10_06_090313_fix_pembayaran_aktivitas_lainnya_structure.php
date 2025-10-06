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
        Schema::table('pembayaran_aktivitas_lainnya', function (Blueprint $table) {
            // Drop foreign key constraint first before dropping the column (if exists)
            try {
                $table->dropForeign(['approved_by']);
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
            
            // Drop unnecessary columns that don't match the form
            $columnsToDrop = ['metode_pembayaran', 'referensi_pembayaran', 'status', 'approved_by', 'approved_at'];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('pembayaran_aktivitas_lainnya', $column)) {
                    $table->dropColumn($column);
                }
            }
            
            // Rename/modify existing columns to match form
            if (Schema::hasColumn('pembayaran_aktivitas_lainnya', 'total_nominal')) {
                $table->renameColumn('total_nominal', 'total_pembayaran');
            }
            if (Schema::hasColumn('pembayaran_aktivitas_lainnya', 'keterangan')) {
                $table->renameColumn('keterangan', 'aktivitas_pembayaran');
            }
            
            // Add new required column for bank selection
            if (!Schema::hasColumn('pembayaran_aktivitas_lainnya', 'pilih_bank')) {
                $table->foreignId('pilih_bank')->after('tanggal_pembayaran')
                      ->constrained('akun_coa')->onDelete('cascade');
            }
                  
            // Modify aktivitas_pembayaran to be required (not nullable)
            $table->text('aktivitas_pembayaran')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran_aktivitas_lainnya', function (Blueprint $table) {
            // Drop the added column
            if (Schema::hasColumn('pembayaran_aktivitas_lainnya', 'pilih_bank')) {
                $table->dropForeign(['pilih_bank']);
                $table->dropColumn('pilih_bank');
            }
            
            // Rename columns back
            if (Schema::hasColumn('pembayaran_aktivitas_lainnya', 'total_pembayaran')) {
                $table->renameColumn('total_pembayaran', 'total_nominal');
            }
            if (Schema::hasColumn('pembayaran_aktivitas_lainnya', 'aktivitas_pembayaran')) {
                $table->renameColumn('aktivitas_pembayaran', 'keterangan');
            }
            
            // Add back the dropped columns
            if (!Schema::hasColumn('pembayaran_aktivitas_lainnya', 'metode_pembayaran')) {
                $table->enum('metode_pembayaran', ['cash', 'transfer', 'check', 'credit_card'])
                      ->after('total_nominal');
            }
            if (!Schema::hasColumn('pembayaran_aktivitas_lainnya', 'referensi_pembayaran')) {
                $table->string('referensi_pembayaran')->nullable()->after('metode_pembayaran');
            }
            if (!Schema::hasColumn('pembayaran_aktivitas_lainnya', 'status')) {
                $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])
                      ->default('draft')->after('keterangan');
            }
            if (!Schema::hasColumn('pembayaran_aktivitas_lainnya', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()
                      ->constrained('users')->onDelete('set null')->after('created_by');
            }
            if (!Schema::hasColumn('pembayaran_aktivitas_lainnya', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            
            // Make keterangan nullable again
            $table->text('keterangan')->nullable()->change();
        });
    }
};
