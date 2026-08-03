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
            $table->text('stowage_rows')->nullable()->after('stowage_bays');
            $table->text('stowage_tiers')->nullable()->after('stowage_rows');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_kapals', function (Blueprint $table) {
            $table->dropColumn(['stowage_rows', 'stowage_tiers']);
        });
    }
};
