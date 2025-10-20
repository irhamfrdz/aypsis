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
        Schema::table('pricelist_gate_ins', function (Blueprint $table) {
            // Check and drop old columns safely
            if (Schema::hasColumn('pricelist_gate_ins', 'kode')) {
                $table->dropColumn('kode');
            }
            if (Schema::hasColumn('pricelist_gate_ins', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
            if (Schema::hasColumn('pricelist_gate_ins', 'catatan')) {
                $table->dropColumn('catatan');
            }

            // Add new columns only if they don't exist
            if (!Schema::hasColumn('pricelist_gate_ins', 'pelabuhan')) {
                $table->string('pelabuhan')->after('id');
            }
            if (!Schema::hasColumn('pricelist_gate_ins', 'kegiatan')) {
                $table->enum('kegiatan', [
                    'BATAL MUAT',
                    'CHANGE VASSEL',
                    'DELIVERY',
                    'DISCHARGE',
                    'DISCHARGE TL',
                    'LOADING',
                    'PENUMPUKAN BPRP',
                    'PERPANJANGAN DELIVERY',
                    'RECEIVING',
                    'RECEIVING LOSING'
                ])->after('pelabuhan');
            }
            if (!Schema::hasColumn('pricelist_gate_ins', 'gudang')) {
                $table->enum('gudang', ['CY', 'DERMAGA', 'SS'])->nullable()->after('kegiatan');
            }
            if (!Schema::hasColumn('pricelist_gate_ins', 'kontainer')) {
                $table->enum('kontainer', ['20', '40'])->nullable()->after('gudang');
            }
            if (!Schema::hasColumn('pricelist_gate_ins', 'muatan')) {
                $table->enum('muatan', ['EMPTY', 'FULL'])->nullable()->after('kontainer');
            }

            // Update tarif column (already exists but ensure it's positioned correctly)
            $table->decimal('tarif', 15, 2)->default(0)->change();

            // Add indexes for new columns (with error handling)
            try {
                $table->index(['pelabuhan', 'kegiatan'], 'idx_pelabuhan_kegiatan');
                $table->index(['gudang', 'kontainer'], 'idx_gudang_kontainer');
                $table->index(['kontainer', 'muatan'], 'idx_kontainer_muatan');
            } catch (Exception $e) {
                // Indexes might already exist, continue
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricelist_gate_ins', function (Blueprint $table) {
            // Drop indexes first
            try {
                $table->dropIndex('idx_pelabuhan_kegiatan');
                $table->dropIndex('idx_gudang_kontainer');
                $table->dropIndex('idx_kontainer_muatan');
            } catch (Exception $e) {
                // Indexes might not exist, continue
            }

            // Drop new columns
            if (Schema::hasColumn('pricelist_gate_ins', 'pelabuhan')) {
                $table->dropColumn('pelabuhan');
            }
            if (Schema::hasColumn('pricelist_gate_ins', 'kegiatan')) {
                $table->dropColumn('kegiatan');
            }
            if (Schema::hasColumn('pricelist_gate_ins', 'gudang')) {
                $table->dropColumn('gudang');
            }
            if (Schema::hasColumn('pricelist_gate_ins', 'kontainer')) {
                $table->dropColumn('kontainer');
            }
            if (Schema::hasColumn('pricelist_gate_ins', 'muatan')) {
                $table->dropColumn('muatan');
            }

            // Add back old columns
            if (!Schema::hasColumn('pricelist_gate_ins', 'kode')) {
                $table->string('kode')->nullable();
            }
            if (!Schema::hasColumn('pricelist_gate_ins', 'keterangan')) {
                $table->text('keterangan')->nullable();
            }
            if (!Schema::hasColumn('pricelist_gate_ins', 'catatan')) {
                $table->text('catatan')->nullable();
            }
        });
    }
};
