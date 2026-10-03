<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_kapal_perijinan', function (Blueprint $table) {
            $table->string('mode', 20)->default('perijinan')->after('biaya_kapal_id');
            $table->string('karantina_source_type', 50)->nullable()->after('mode');
            $table->unsignedBigInteger('karantina_source_id')->nullable()->after('karantina_source_type');
            $table->string('karantina_nomor_dokumen')->nullable()->after('karantina_source_id');

            $table->index(
                ['karantina_source_type', 'karantina_source_id'],
                'biaya_kapal_perijinan_karantina_source_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('biaya_kapal_perijinan', function (Blueprint $table) {
            $table->dropIndex('biaya_kapal_perijinan_karantina_source_index');
            $table->dropColumn([
                'mode',
                'karantina_source_type',
                'karantina_source_id',
                'karantina_nomor_dokumen',
            ]);
        });
    }
};
