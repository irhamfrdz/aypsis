<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropBpjsJamsostekFromKaryawans extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (Schema::hasColumn('karyawans', 'bpjs_jamsostek')) {
            Schema::table('karyawans', function (Blueprint $table) {
                $table->dropColumn('bpjs_jamsostek');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (!Schema::hasColumn('karyawans', 'bpjs_jamsostek')) {
            Schema::table('karyawans', function (Blueprint $table) {
                $table->string('bpjs_jamsostek')->nullable()->after('jkn');
            });
        }
    }
}
