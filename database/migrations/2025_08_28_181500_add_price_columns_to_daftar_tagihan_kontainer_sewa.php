<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('daftar_tagihan_kontainer_sewa')) {
            return;
        }

        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            if (!Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'dpp')) {
                $table->decimal('dpp', 15, 2)->nullable()->after('tarif');
            }
            if (!Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'dpp_nilai_lain')) {
                $table->decimal('dpp_nilai_lain', 15, 2)->nullable()->after('dpp');
            }
            if (!Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'ppn')) {
                $table->decimal('ppn', 15, 2)->nullable()->after('dpp_nilai_lain');
            }
            if (!Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'pph')) {
                $table->decimal('pph', 15, 2)->nullable()->after('ppn');
            }
            if (!Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'grand_total')) {
                $table->decimal('grand_total', 18, 2)->nullable()->after('pph');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('daftar_tagihan_kontainer_sewa')) {
            return;
        }

        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            // dropColumn may require doctrine/dbal; attempt guarded drops
            if (Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'grand_total')) {
                $table->dropColumn('grand_total');
            }
            if (Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'pph')) {
                $table->dropColumn('pph');
            }
            if (Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'ppn')) {
                $table->dropColumn('ppn');
            }
            if (Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'dpp_nilai_lain')) {
                $table->dropColumn('dpp_nilai_lain');
            }
            if (Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'dpp')) {
                $table->dropColumn('dpp');
            }
        });
    }
};
