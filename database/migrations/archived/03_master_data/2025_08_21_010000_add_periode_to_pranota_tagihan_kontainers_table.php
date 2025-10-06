<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pranota_tagihan_kontainers', function (Blueprint $table) {
            $table->string('periode')->nullable()->after('tanggal');
        });
    }

    public function down()
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('pranota_tagihan_kontainers', function (Blueprint $table) {
            $table->dropColumn('periode');
        });
    }
};
