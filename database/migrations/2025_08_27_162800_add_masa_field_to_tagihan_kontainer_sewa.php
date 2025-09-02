<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('tagihan_kontainer_sewa', 'masa')) {
            Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
                $table->string('masa', 255)->nullable()->after('group_code');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('tagihan_kontainer_sewa', 'masa')) {
            Schema::table('tagihan_kontainer_sewa', function (Blueprint $table) {
                $table->dropColumn('masa');
            });
        }
    }
};
