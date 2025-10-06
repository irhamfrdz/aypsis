<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
            if (Schema::hasColumn('tagihan_kontainer_sewa', 'masa_awal')) {
                $table->dropColumn('masa_awal');
            }
            if (Schema::hasColumn('tagihan_kontainer_sewa', 'masa_akhir')) {
                $table->dropColumn('masa_akhir');
            }
        });
    }

    public function down()
    {
        Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'masa_awal')) {
                $table->date('masa_awal')->nullable()->after('tanggal_harga_awal');
            }
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'masa_akhir')) {
                $table->date('masa_akhir')->nullable()->after('tanggal_harga_akhir');
            }
        });
    }
};
