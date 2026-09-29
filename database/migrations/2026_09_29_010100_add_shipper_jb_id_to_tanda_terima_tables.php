<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['tanda_terimas', 'tanda_terima_tanpa_surat_jalan', 'tanda_terimas_lcl'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedBigInteger('shipper_jb_id')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        foreach (['tanda_terimas', 'tanda_terima_tanpa_surat_jalan', 'tanda_terimas_lcl'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropIndex(['shipper_jb_id']);
                $table->dropColumn('shipper_jb_id');
            });
        }
    }
};
