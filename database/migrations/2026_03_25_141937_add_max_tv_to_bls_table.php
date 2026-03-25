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
        Schema::table('bls', function (Blueprint $table) {
            $table->decimal('max_tv', 15, 3)->nullable()->after('volume')->comment('Maximum of tonnage and volume');
        });

        // Backfill existing data
        \App\Models\Bl::all()->each(function ($bl) {
            $bl->update([
                'max_tv' => max((float)$bl->tonnage, (float)$bl->volume)
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bls', function (Blueprint $table) {
            $table->dropColumn('max_tv');
        });
    }
};
