<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pranota_bpjs_headers', function (Blueprint $table) {
            $table->json('snapshot_rumus')->nullable();
        });
        Schema::table('pranota_bpjs_details', function (Blueprint $table) {
            $table->json('snapshot_bpjs')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pranota_bpjs_details', fn (Blueprint $table) => $table->dropColumn('snapshot_bpjs'));
        Schema::table('pranota_bpjs_headers', fn (Blueprint $table) => $table->dropColumn('snapshot_rumus'));
    }
};
