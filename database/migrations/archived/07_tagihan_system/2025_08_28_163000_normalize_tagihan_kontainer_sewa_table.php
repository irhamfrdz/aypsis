<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NormalizeTagihanKontainerSewaTable extends Migration
{
    public function up()
    {
        // Keep only the fields used by the index view and remove historical/unused columns.
        Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
            // Ensure core columns exist
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'vendor')) {
                $table->string('vendor')->nullable()->after('id');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'nomor_kontainer')) {
                $table->text('nomor_kontainer')->nullable()->after('vendor');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'group_code')) {
                $table->string('group_code')->nullable()->after('nomor_kontainer');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'group')) {
                $table->string('group')->nullable()->after('group_code');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'tanggal_harga_awal')) {
                $table->date('tanggal_harga_awal')->nullable()->after('group');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'tanggal_harga_akhir')) {
                $table->date('tanggal_harga_akhir')->nullable()->after('tanggal_harga_awal');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'periode')) {
                $table->integer('periode')->nullable()->after('tanggal_harga_akhir');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'massa')) {
                $table->decimal('massa', 12, 2)->nullable()->after('periode');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'dpp')) {
                $table->decimal('dpp', 15, 2)->nullable()->after('massa');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'dpp_nilai_lain')) {
                $table->decimal('dpp_nilai_lain', 15, 2)->nullable()->after('dpp');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'ppn')) {
                $table->decimal('ppn', 15, 2)->nullable()->after('dpp_nilai_lain');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'pph')) {
                $table->decimal('pph', 15, 2)->nullable()->after('ppn');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'grand_total')) {
                $table->decimal('grand_total', 18, 2)->nullable()->after('pph');
            }

            // Optional: keep a free-text note field used elsewhere
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('grand_total');
            }

            // Drop columns that are not used in the single-table view
            $toDrop = ['ukuran_kontainer','tarif','harga','status_pembayaran','nomor_pranota','is_pranota','tanggal_checkpoint_supir','masa'];
            foreach ($toDrop as $c) {
                if (Schema::hasColumn('tagihan_kontainer_sewa', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }

    public function down()
    {
        // Attempt to restore dropped columns with minimal types (non-destructive fallback)
        Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'ukuran_kontainer')) $table->string('ukuran_kontainer')->nullable();
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'tarif')) $table->string('tarif')->nullable();
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'harga')) $table->decimal('harga',15,2)->nullable();
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'status_pembayaran')) $table->string('status_pembayaran')->nullable();
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'nomor_pranota')) $table->string('nomor_pranota')->nullable();
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'is_pranota')) $table->boolean('is_pranota')->default(false);
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'tanggal_checkpoint_supir')) $table->timestamp('tanggal_checkpoint_supir')->nullable();
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'masa')) $table->string('masa')->nullable();
        });
    }
}
