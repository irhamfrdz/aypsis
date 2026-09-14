<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawan_tidak_tetaps', function (Blueprint $table) {
            $table->string('jkn')->nullable()->after('status_pajak');
            $table->string('dpp_jkn')->nullable()->after('jkn');
            $table->string('group_jkn')->nullable()->after('dpp_jkn');
            $table->string('no_ketenagakerjaan')->nullable()->after('group_jkn');
            $table->string('dpp_bp_jamsostek')->nullable()->after('no_ketenagakerjaan');
            $table->string('group_bp_jamsostek')->nullable()->after('dpp_bp_jamsostek');
            $table->string('cabang_bpjs')->nullable()->after('group_bp_jamsostek');
        });
    }

    public function down(): void
    {
        Schema::table('karyawan_tidak_tetaps', function (Blueprint $table) {
            $table->dropColumn([
                'jkn',
                'dpp_jkn',
                'group_jkn',
                'no_ketenagakerjaan',
                'dpp_bp_jamsostek',
                'group_bp_jamsostek',
                'cabang_bpjs',
            ]);
        });
    }
};
