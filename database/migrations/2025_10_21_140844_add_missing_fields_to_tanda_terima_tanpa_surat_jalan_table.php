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
            // Add missing fields based on the form

            // Basic info fields
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'nomor_tanda_terima')) {
                $table->string('nomor_tanda_terima')->nullable()->after('no_tanda_terima');
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'nomor_surat_jalan_customer')) {
                $table->string('nomor_surat_jalan_customer')->nullable()->after('nomor_tanda_terima');
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'term_id')) {
                $table->unsignedBigInteger('term_id')->nullable()->after('nomor_surat_jalan_customer');
            }

            // Contact fields
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'telepon')) {
                $table->string('telepon')->nullable()->after('pengirim');
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'pic')) {
                $table->string('pic')->nullable()->after('telepon');
            }

            // Activity field
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'aktifitas')) {
                $table->string('aktifitas')->nullable()->after('jenis_barang');
            }

            // Transportation fields
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'no_kontainer')) {
                $table->string('no_kontainer')->nullable()->after('tujuan_pengiriman');
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'no_seal')) {
                $table->string('no_seal')->nullable()->after('estimasi_naik_kapal');
            }

            // Dimension fields for backward compatibility
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'tonase')) {
                $table->decimal('tonase', 10, 2)->nullable()->after('satuan_berat');
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'panjang')) {
                $table->decimal('panjang', 10, 2)->nullable()->after('tonase');
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'lebar')) {
                $table->decimal('lebar', 10, 2)->nullable()->after('panjang');
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'tinggi')) {
                $table->decimal('tinggi', 10, 2)->nullable()->after('lebar');
            }
            if (!Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'meter_kubik')) {
                $table->decimal('meter_kubik', 12, 6)->nullable()->after('tinggi');
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

            if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'nomor_tanda_terima')) {
                $columnsToRemove[] = 'nomor_tanda_terima';
            }
            if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'nomor_surat_jalan_customer')) {
                $columnsToRemove[] = 'nomor_surat_jalan_customer';
            }
            if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'term_id')) {
                $columnsToRemove[] = 'term_id';
            }
            if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'pic')) {
                $columnsToRemove[] = 'pic';
            }
            if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'telepon')) {
                $columnsToRemove[] = 'telepon';
            }
            if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'aktifitas')) {
                $columnsToRemove[] = 'aktifitas';
            }
            if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'no_kontainer')) {
                $columnsToRemove[] = 'no_kontainer';
            }
            if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'no_seal')) {
                $columnsToRemove[] = 'no_seal';
            }
            if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'panjang')) {
                $columnsToRemove[] = 'panjang';
            }
            if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'lebar')) {
                $columnsToRemove[] = 'lebar';
            }
            if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'tinggi')) {
                $columnsToRemove[] = 'tinggi';
            }
            if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'meter_kubik')) {
                $columnsToRemove[] = 'meter_kubik';
            }
            if (Schema::hasColumn('tanda_terima_tanpa_surat_jalan', 'tonase')) {
                $columnsToRemove[] = 'tonase';
            }

            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
};
