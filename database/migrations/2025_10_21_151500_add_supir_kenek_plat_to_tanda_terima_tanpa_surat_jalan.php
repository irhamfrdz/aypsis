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
        Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            // Add supir, kenek, and no_plat fields back
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'supir')) {
                $table->string('supir')->default('Supir Customer')->after('pic');
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'kenek')) {
                $table->string('kenek')->default('Kenek Customer')->nullable()->after('supir');
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'no_plat')) {
                $table->string('no_plat', 20)->nullable()->after('kenek');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            $columnsToRemove = [];

            if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'supir')) {
                $columnsToRemove[] = 'supir';
            }
            if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'kenek')) {
                $columnsToRemove[] = 'kenek';
            }
            if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'no_plat')) {
                $columnsToRemove[] = 'no_plat';
            }

            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
};
