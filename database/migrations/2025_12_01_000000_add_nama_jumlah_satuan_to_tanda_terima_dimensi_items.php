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
        if (Schema::hasTable('tanda_terima_dimensi_items')) {
            Schema::table('tanda_terima_dimensi_items', function (Blueprint $table) {
                if (!Schema::hasColumn('tanda_terima_dimensi_items', 'nama_barang')) {
                    $table->string('nama_barang')->nullable()->after('tanda_terima_tanpa_surat_jalan_id');
                }
                if (!Schema::hasColumn('tanda_terima_dimensi_items', 'jumlah')) {
                    $table->integer('jumlah')->default(1)->after('nama_barang');
                }
                if (!Schema::hasColumn('tanda_terima_dimensi_items', 'satuan')) {
                    $table->string('satuan', 50)->default('unit')->after('jumlah');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tanda_terima_dimensi_items')) {
            Schema::table('tanda_terima_dimensi_items', function (Blueprint $table) {
                $columnsToRemove = [];
                
                if (Schema::hasColumn('tanda_terima_dimensi_items', 'nama_barang')) {
                    $columnsToRemove[] = 'nama_barang';
                }
                if (Schema::hasColumn('tanda_terima_dimensi_items', 'jumlah')) {
                    $columnsToRemove[] = 'jumlah';
                }
                if (Schema::hasColumn('tanda_terima_dimensi_items', 'satuan')) {
                    $columnsToRemove[] = 'satuan';
                }

                if (!empty($columnsToRemove)) {
                    $table->dropColumn($columnsToRemove);
                }
            });
        }
    }
};
