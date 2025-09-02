<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCatatanToKaryawans extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasColumn('karyawans', 'catatan')) {
            Schema::table('karyawans', function (Blueprint $table) {
                $table->text('catatan')->nullable()->after('tanggal_berhenti_sebelumnya');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (Schema::hasColumn('karyawans', 'catatan')) {
            Schema::table('karyawans', function (Blueprint $table) {
                $table->dropColumn('catatan');
            });
        }
    }
}
