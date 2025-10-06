<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        Schema::table('checkpoints', function (Blueprint $table) {
            $table->date('tanggal_checkpoint')->nullable()->after('id');
        });
    }

    public function down()
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('checkpoints', function (Blueprint $table) {
            $table->dropColumn('tanggal_checkpoint');
        });
    }
};
