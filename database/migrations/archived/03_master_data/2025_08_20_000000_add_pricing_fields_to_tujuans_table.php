<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tujuans', function (Blueprint $table) {
            // location/hierarchy
            $table->string('cabang')->nullable()->after('nama_tujuan');
            $table->string('wilayah')->nullable()->after('cabang');
            $table->string('rute')->nullable()->after('wilayah');

            // pricing for 20ft
            $table->decimal('uang_jalan_20', 15, 2)->default(0)->after('uang_jalan');
            $table->decimal('ongkos_truk_20', 15, 2)->default(0)->after('uang_jalan_20');

            // pricing for 40ft
            $table->decimal('uang_jalan_40', 15, 2)->default(0)->after('ongkos_truk_20');
            $table->decimal('ongkos_truk_40', 15, 2)->default(0)->after('uang_jalan_40');

            // antar lokasi pricing
            $table->decimal('antar_20', 15, 2)->default(0)->after('ongkos_truk_40');
            $table->decimal('antar_40', 15, 2)->default(0)->after('antar_20');
        });
    }

    public function down()
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('tujuans', function (Blueprint $table) {
            $table->dropColumn([
                'cabang', 'wilayah', 'rute',
                'uang_jalan_20', 'ongkos_truk_20',
                'uang_jalan_40', 'ongkos_truk_40',
                'antar_20', 'antar_40'
            ]);
        });
    }
};
