<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNomorPranotaToTagihanKontainerSewaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
            $table->string('nomor_pranota')->nullable()->after('group_code');
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
            $table->dropColumn('nomor_pranota');
        });
    }
}
