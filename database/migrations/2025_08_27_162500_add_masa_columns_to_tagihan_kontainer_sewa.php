<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('tagihan_kontainer_sewa', 'masa_awal')) {
            Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
                $table->date('masa_awal')->nullable()->after('tanggal_harga_awal');
                $table->date('masa_akhir')->nullable()->after('tanggal_harga_akhir');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('tagihan_kontainer_sewa', 'masa_awal') || Schema::hasColumn('tagihan_kontainer_sewa', 'masa_akhir')) {
            Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
                if (Schema::hasColumn('tagihan_kontainer_sewa', 'masa_awal')) $table->dropColumn('masa_awal');
                if (Schema::hasColumn('tagihan_kontainer_sewa', 'masa_akhir')) $table->dropColumn('masa_akhir');
            });
        }
    }
};
