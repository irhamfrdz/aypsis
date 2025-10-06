<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('kontainers')) {
            Schema::table('kontainers', function (Blueprint $table) {
                if (!Schema::hasColumn('kontainers', 'harga_satuan')) {
                    // store as decimal with generous precision
                    $table->decimal('harga_satuan', 15, 2)->nullable()->after('status');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('kontainers')) {
            Schema::table('kontainers', function (Blueprint $table) {
                if (Schema::hasColumn('kontainers', 'harga_satuan')) {
                    $table->dropColumn('harga_satuan');
                }
            });
        }
    }
};
