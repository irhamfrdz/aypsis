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
            // Check which columns exist and only add the ones that don't exist
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'nomor_surat_jalan_customer')) {
                $table->string('nomor_surat_jalan_customer')->nullable()->after('no_tanda_terima');
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'term')) {
                $table->string('term')->nullable()->after('status');
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'pic')) {
                $table->string('pic')->nullable()->after('alamat_penerima');
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'telepon')) {
                $table->string('telepon')->nullable()->after('pic');
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'nama_barang')) {
                $table->string('nama_barang')->nullable()->after('jenis_barang');
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'aktifitas')) {
                $table->string('aktifitas')->nullable()->after('nama_barang');
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'jenis_pengiriman')) {
                $table->string('jenis_pengiriman')->nullable()->after('tujuan_pengiriman');
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'kenek')) {
                $table->string('kenek')->nullable()->after('supir');
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'no_kontainer')) {
                $table->string('no_kontainer')->nullable()->after('no_plat');
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'no_seal')) {
                $table->string('no_seal')->nullable()->after('no_kontainer');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            // Drop the new fields only if they exist
            $columnsToCheck = [
                'nomor_surat_jalan_customer',
                'term',
                'pic',
                'telepon',
                'nama_barang',
                'aktifitas',
                'jenis_pengiriman',
                'kenek',
                'no_kontainer',
                'no_seal'
            ];

            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
