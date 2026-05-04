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
        if (!Schema::hasColumn('tanda_terimas', 'ukuran')) {
            Schema::table('tanda_terimas', function (Blueprint $blueprint) {
                $blueprint->string('ukuran')->nullable()->after('nama_barang');
            });
        }

        if (!Schema::hasColumn('tanda_terima_dimensi_items', 'ukuran')) {
            Schema::table('tanda_terima_dimensi_items', function (Blueprint $blueprint) {
                $blueprint->string('ukuran')->nullable()->after('nama_barang');
            });
        }
        
        if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'ukuran')) {
            Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $blueprint) {
                $blueprint->string('ukuran')->nullable()->after('nama_barang');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('tanda_terimas', 'ukuran')) {
            Schema::table('tanda_terimas', function (Blueprint $blueprint) {
                $blueprint->dropColumn('ukuran');
            });
        }

        if (Schema::hasColumn('tanda_terima_dimensi_items', 'ukuran')) {
            Schema::table('tanda_terima_dimensi_items', function (Blueprint $blueprint) {
                $blueprint->dropColumn('ukuran');
            });
        }
        
        if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'ukuran')) {
            Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $blueprint) {
                $blueprint->dropColumn('ukuran');
            });
        }
    }
};
