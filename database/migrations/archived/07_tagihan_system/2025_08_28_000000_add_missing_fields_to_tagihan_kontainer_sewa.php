<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingFieldsToTagihanKontainerSewa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'nomor_kontainer')) {
                $table->text('nomor_kontainer')->nullable()->after('harga');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'dpp')) {
                $table->decimal('dpp', 15, 2)->nullable()->after('nomor_kontainer');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'ppn')) {
                $table->decimal('ppn', 15, 2)->nullable()->after('dpp');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'pph')) {
                $table->decimal('pph', 15, 2)->nullable()->after('ppn');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'grand_total')) {
                $table->decimal('grand_total', 15, 2)->nullable()->after('pph');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'nomor_pranota')) {
                $table->string('nomor_pranota')->nullable()->after('group_code');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'is_pranota')) {
                $table->boolean('is_pranota')->default(false)->after('nomor_pranota');
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
        Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
            if (Schema::hasColumn('tagihan_kontainer_sewa', 'grand_total')) {
                $table->dropColumn(['grand_total', 'pph', 'ppn', 'dpp']);
            }
            if (Schema::hasColumn('tagihan_kontainer_sewa', 'nomor_kontainer')) {
                $table->dropColumn('nomor_kontainer');
            }
            if (Schema::hasColumn('tagihan_kontainer_sewa', 'nomor_pranota')) {
                $table->dropColumn(['nomor_pranota', 'is_pranota']);
            }
        });
    }
}
