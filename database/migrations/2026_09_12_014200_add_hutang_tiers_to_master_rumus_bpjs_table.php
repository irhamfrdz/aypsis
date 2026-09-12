<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_rumus_bpjs', function (Blueprint $table) {
            // Kolom JSON untuk menyimpan tier DPP → Potongan
            // Format: [{"dpp": 2200000, "potongan": 14000}, {"dpp": 4450000, "potongan": 25000}]
            $table->json('hutang_tiers')->nullable()->after('hutang_persen');
        });
    }

    public function down(): void
    {
        Schema::table('master_rumus_bpjs', function (Blueprint $table) {
            $table->dropColumn('hutang_tiers');
        });
    }
};
