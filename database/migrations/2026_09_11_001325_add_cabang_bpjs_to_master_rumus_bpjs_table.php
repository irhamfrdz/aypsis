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
        Schema::table('master_rumus_bpjs', function (Blueprint $table) {
            $table->string('cabang_bpjs')->nullable()->after('group_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_rumus_bpjs', function (Blueprint $table) {
            $table->dropColumn('cabang_bpjs');
        });
    }
};
