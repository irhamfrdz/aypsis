<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->string('tipe_jkn')->nullable()->after('group_jkn');
            $table->string('tipe_bp_jamsostek')->nullable()->after('group_bp_jamsostek');
        });
    }

    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropColumn(['tipe_jkn', 'tipe_bp_jamsostek']);
        });
    }
};
