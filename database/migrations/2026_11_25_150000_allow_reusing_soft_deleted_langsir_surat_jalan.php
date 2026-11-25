<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('langsir_batams', function (Blueprint $table) {
            $table->dropUnique('langsir_batams_no_surat_jalan_unique');
        });

        Schema::table('langsir_batams', function (Blueprint $table) {
            $table->string('active_no_surat_jalan')->nullable()
                ->virtualAs('CASE WHEN deleted_at IS NULL THEN no_surat_jalan ELSE NULL END');
            $table->unique('active_no_surat_jalan', 'langsir_batams_active_sj_unique');
        });
    }

    public function down(): void
    {
        Schema::table('langsir_batams', function (Blueprint $table) {
            $table->dropUnique('langsir_batams_active_sj_unique');
            $table->dropColumn('active_no_surat_jalan');
            $table->unique('no_surat_jalan');
        });
    }
};
