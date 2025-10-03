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
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            // Add indexes for commonly filtered/searched columns
            $table->index('vendor', 'idx_vendor');
            $table->index('nomor_kontainer', 'idx_nomor_kontainer');
            $table->index('size', 'idx_size');
            $table->index('periode', 'idx_periode');
            $table->index('group', 'idx_group');
            $table->index('status_pranota', 'idx_status_pranota');
            $table->index('tanggal_awal', 'idx_tanggal_awal');
            $table->index('tanggal_akhir', 'idx_tanggal_akhir');

            // Composite indexes for common query patterns
            $table->index(['vendor', 'nomor_kontainer'], 'idx_vendor_kontainer');
            $table->index(['nomor_kontainer', 'periode'], 'idx_kontainer_periode');
            $table->index(['vendor', 'periode'], 'idx_vendor_periode');
            $table->index(['group', 'periode'], 'idx_group_periode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            // Drop individual indexes
            $table->dropIndex('idx_vendor');
            $table->dropIndex('idx_nomor_kontainer');
            $table->dropIndex('idx_size');
            $table->dropIndex('idx_periode');
            $table->dropIndex('idx_group');
            $table->dropIndex('idx_status_pranota');
            $table->dropIndex('idx_tanggal_awal');
            $table->dropIndex('idx_tanggal_akhir');

            // Drop composite indexes
            $table->dropIndex('idx_vendor_kontainer');
            $table->dropIndex('idx_kontainer_periode');
            $table->dropIndex('idx_vendor_periode');
            $table->dropIndex('idx_group_periode');
        });
    }
};
