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
            // Drop old columns (foreign keys will be dropped automatically when columns are dropped)

            // Drop old columns
            $table->dropColumn([
                'kode',
                'keterangan',
                'catatan'
            ]);

            // Add new columns
            $table->string('pelabuhan')->after('id');
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
            $table->enum('biaya', [
                'ADMINISTRASI',
                'DERMAGA',
                'HAULAGE',
                'LOLO',
                'MASA 1A',
                'MASA 1B',
                'MASA2',
                'STEVEDORING',
                'STRIPPING',
                'STUFFING'
            ])->after('kegiatan');
            $table->enum('gudang', ['CY', 'DERMAGA', 'SS'])->nullable()->after('biaya');
            $table->enum('kontainer', ['20', '40'])->nullable()->after('gudang');
            $table->enum('muatan', ['EMPTY', 'FULL'])->nullable()->after('kontainer');

            // Update tarif column (already exists but ensure it's positioned correctly)
            $table->decimal('tarif', 15, 2)->default(0)->after('muatan')->change();

            // Add indexes for new columns
            $table->index(['pelabuhan', 'kegiatan']);
            $table->index(['biaya', 'gudang']);
            $table->index(['kontainer', 'muatan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricelist_gate_ins', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn([
                'pelabuhan',
                'kegiatan',
                'biaya',
                'gudang',
                'kontainer',
                'muatan'
            ]);

            // Restore old columns (no foreign keys needed for rollback)
        });
    }
};
