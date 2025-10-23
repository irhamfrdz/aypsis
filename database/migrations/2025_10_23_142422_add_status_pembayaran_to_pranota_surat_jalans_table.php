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
        Schema::table('pranota_surat_jalans', function (Blueprint $table) {
            // Rename existing status column to status_pranota for clarity
            $table->renameColumn('status', 'status_pranota');

            // Add new status_pembayaran column
            $table->enum('status_pembayaran', ['unpaid', 'partial', 'paid', 'cancelled'])
                  ->default('unpaid')
                  ->after('total_amount')
                  ->comment('Status pembayaran pranota: unpaid, partial, paid, cancelled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_surat_jalans', function (Blueprint $table) {
            $table->dropColumn('status_pembayaran');
            $table->renameColumn('status_pranota', 'status');
        });
    }
};
