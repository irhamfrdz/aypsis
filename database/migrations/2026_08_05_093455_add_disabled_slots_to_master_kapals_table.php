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
        Schema::table('master_kapals', function (Blueprint $table) {
            $table->json('disabled_slots')->nullable()->after('stowage_tiers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_kapals', function (Blueprint $table) {
            $table->dropColumn('disabled_slots');
        });
    }
};
