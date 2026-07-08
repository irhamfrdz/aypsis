<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mesins', function (Blueprint $table) {
            if (!Schema::hasColumn('mesins', 'ip_address')) {
                $table->string('ip_address')->nullable()->after('tipe_mesin');
            }
            if (!Schema::hasColumn('mesins', 'port')) {
                $table->integer('port')->default(4370)->after('ip_address');
            }
            if (!Schema::hasColumn('mesins', 'comm_key')) {
                $table->integer('comm_key')->default(0)->after('port');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mesins', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('mesins', 'ip_address')) {
                $columns[] = 'ip_address';
            }
            if (Schema::hasColumn('mesins', 'port')) {
                $columns[] = 'port';
            }
            if (Schema::hasColumn('mesins', 'comm_key')) {
                $columns[] = 'comm_key';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
