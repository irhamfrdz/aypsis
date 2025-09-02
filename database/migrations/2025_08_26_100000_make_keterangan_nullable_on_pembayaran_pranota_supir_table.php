<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeKeteranganNullableOnPembayaranPranotaSupirTable extends Migration
{
    /**
     * Run the migrations.
     *
     * Note: changing column nullability requires the doctrine/dbal package when
     * running against MySQL/Postgres in Laravel. If you don't have it installed,
     * run: composer require doctrine/dbal
     */
    public function up()
    {
        if (!Schema::hasTable('pembayaran_pranota_supir')) {
            return;
        }

        Schema::table('pembayaran_pranota_supir', function (Blueprint $table) {
            $table->text('keterangan')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (!Schema::hasTable('pembayaran_pranota_supir')) {
            return;
        }

        Schema::table('pembayaran_pranota_supir', function (Blueprint $table) {
            $table->text('keterangan')->nullable(false)->change();
        });
    }
}
