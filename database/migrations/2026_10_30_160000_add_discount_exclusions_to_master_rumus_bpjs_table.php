<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_rumus_bpjs', fn (Blueprint $table) => $table->json('diskon_pengecualian')->nullable());
    }

    public function down(): void
    {
        Schema::table('master_rumus_bpjs', fn (Blueprint $table) => $table->dropColumn('diskon_pengecualian'));
    }
};
