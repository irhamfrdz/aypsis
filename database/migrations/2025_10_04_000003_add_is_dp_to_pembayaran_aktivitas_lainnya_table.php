<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsDpToPembayaranAktivitasLainnyaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pembayaran_aktivitas_lainnya', function (Blueprint $table) {
            $table->boolean('is_dp')->default(false)->after('aktivitas_pembayaran');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pembayaran_aktivitas_lainnya', function (Blueprint $table) {
            $table->dropColumn('is_dp');
        });
    }
}
