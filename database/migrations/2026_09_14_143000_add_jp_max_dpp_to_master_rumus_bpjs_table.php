<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_rumus_bpjs', function (Blueprint $table) {
            $table->decimal('jp_max_dpp', 15, 2)->nullable()->after('jp_hutang');
        });
    }

    public function down(): void
    {
        Schema::table('master_rumus_bpjs', function (Blueprint $table) {
            $table->dropColumn('jp_max_dpp');
        });
    }
};
