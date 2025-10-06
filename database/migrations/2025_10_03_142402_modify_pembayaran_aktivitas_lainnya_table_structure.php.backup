<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPembayaranAktivitasLainnyaTableStructure142402 extends Migration
{
    /**
     * Run the migrations.
     * Modify table to match the create form requirements and drop unnecessary columns.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pembayaran_aktivitas_lainnya', function (Blueprint $table) {
            // Drop foreign key constraint first before dropping the column (if exists)
            try {
                $table->dropForeign(['approved_by']);
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
            
            // Drop unnecessary columns that don't match the form
            $table->dropColumn([
                'metode_pembayaran',
                'referensi_pembayaran', 
                'status',
                'approved_by',
                'approved_at'
            ]);
            
            // Rename/modify existing columns to match form
            $table->renameColumn('total_nominal', 'total_pembayaran');
            $table->renameColumn('keterangan', 'aktivitas_pembayaran');
            
            // Add new required column for bank selection
            $table->foreignId('pilih_bank')->after('tanggal_pembayaran')
                  ->constrained('akun_coa')->onDelete('cascade');
                  
            // Modify aktivitas_pembayaran to be required (not nullable)
            $table->text('aktivitas_pembayaran')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     * Restore the original table structure.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pembayaran_aktivitas_lainnya', function (Blueprint $table) {
            // Drop the added column
            $table->dropForeign(['pilih_bank']);
            $table->dropColumn('pilih_bank');
            
            // Rename columns back
            $table->renameColumn('total_pembayaran', 'total_nominal');
            $table->renameColumn('aktivitas_pembayaran', 'keterangan');
            
            // Add back the dropped columns
            $table->enum('metode_pembayaran', ['cash', 'transfer', 'check', 'credit_card'])
                  ->after('total_nominal');
            $table->string('referensi_pembayaran')->nullable()->after('metode_pembayaran');
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])
                  ->default('draft')->after('keterangan');
            $table->foreignId('approved_by')->nullable()
                  ->constrained('users')->onDelete('set null')->after('created_by');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            
            // Make keterangan nullable again
            $table->text('keterangan')->nullable()->change();
        });
    }
}