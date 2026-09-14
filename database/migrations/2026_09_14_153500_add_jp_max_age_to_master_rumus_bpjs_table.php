<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_rumus_bpjs', function (Blueprint $table) {
            $table->unsignedInteger('jp_max_age')->nullable()->after('jp_max_dpp');
        });
    }

    public function down(): void
    {
        Schema::table('master_rumus_bpjs', function (Blueprint $table) {
            $table->dropColumn('jp_max_age');
        });
    }
};
