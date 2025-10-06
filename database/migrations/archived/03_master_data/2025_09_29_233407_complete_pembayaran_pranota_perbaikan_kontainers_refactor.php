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
        Schema::table('pembayaran_pranota_perbaikan_kontainers', function (Blueprint $table) {
            // Remove remaining old columns
            if (Schema::hasColumn('pembayaran_pranota_perbaikan_kontainers', 'pranota_perbaikan_kontainer_id')) {
                $table->dropColumn('pranota_perbaikan_kontainer_id');
            }
            if (Schema::hasColumn('pembayaran_pranota_perbaikan_kontainers', 'nomor_invoice')) {
                $table->dropColumn('nomor_invoice');
            }
            if (Schema::hasColumn('pembayaran_pranota_perbaikan_kontainers', 'metode_pembayaran')) {
                $table->dropColumn('metode_pembayaran');
            }
            if (Schema::hasColumn('pembayaran_pranota_perbaikan_kontainers', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
            if (Schema::hasColumn('pembayaran_pranota_perbaikan_kontainers', 'created_by')) {
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('pembayaran_pranota_perbaikan_kontainers', 'updated_by')) {
                $table->dropColumn('updated_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration completes the refactoring, reverse would be complex
        // In production, you would need to restore from backup if rollback is needed
    }
};
