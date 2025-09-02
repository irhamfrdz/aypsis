<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceFieldsToTagihanKontainerSewaKontainers extends Migration
{
    public function up()
    {
        Schema::table('tagihan_kontainer_sewa_kontainers', function (Blueprint $table) {
            if (!Schema::hasColumn('tagihan_kontainer_sewa_kontainers', 'harga')) {
                $table->decimal('harga', 15, 2)->nullable()->after('kontainer_id');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa_kontainers', 'dpp')) {
                $table->decimal('dpp', 15, 2)->nullable()->after('harga');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa_kontainers', 'ppn')) {
                $table->decimal('ppn', 15, 2)->nullable()->after('dpp');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa_kontainers', 'pph')) {
                $table->decimal('pph', 15, 2)->nullable()->after('ppn');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa_kontainers', 'grand_total')) {
                $table->decimal('grand_total', 15, 2)->nullable()->after('pph');
            }
        });
    }

    public function down()
    {
        Schema::table('tagihan_kontainer_sewa_kontainers', function (Blueprint $table) {
            if (Schema::hasColumn('tagihan_kontainer_sewa_kontainers', 'grand_total')) {
                $table->dropColumn(['grand_total','pph','ppn','dpp','harga']);
            }
        });
    }
}
