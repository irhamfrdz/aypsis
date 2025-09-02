<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusPembayaranToTagihanKontainerSewa extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('tagihan_kontainer_sewa')) return;
        Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
            if (!Schema::hasColumn('tagihan_kontainer_sewa', 'status_pembayaran')) {
                $table->string('status_pembayaran', 64)->nullable()->default('Belum Pembayaran')->after('keterangan');
            }
        });
    }

    public function down()
    {
        if (!Schema::hasTable('tagihan_kontainer_sewa')) return;
        Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
            if (Schema::hasColumn('tagihan_kontainer_sewa', 'status_pembayaran')) {
                $table->dropColumn('status_pembayaran');
            }
        });
    }
}
