<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('permohonan_kontainers')) {
            Schema::table('permohonan_kontainers', function (Blueprint $table) {
                if (!Schema::hasColumn('permohonan_kontainers', 'is_paid')) {
                    $table->boolean('is_paid')->default(false)->after('kontainer_id');
                    $table->index(['permohonan_id', 'is_paid'], 'permohonan_is_paid_idx');
                }
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('permohonan_kontainers')) {
            Schema::table('permohonan_kontainers', function (Blueprint $table) {
                if (Schema::hasColumn('permohonan_kontainers', 'is_paid')) {
                    $table->dropIndex('permohonan_is_paid_idx');
                    $table->dropColumn('is_paid');
                }
            });
        }
    }
};
