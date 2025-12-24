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
        Schema::table('biaya_kapals', function (Blueprint $table) {
            // Drop any existing indexes on nama_kapal if they exist
            try {
                $table->dropIndex(['nama_kapal']);
            } catch (\Exception $e) {
                // Index might not exist, continue
            }
        });
        
        // Now change to JSON
        Schema::table('biaya_kapals', function (Blueprint $table) {
            $table->json('nama_kapal')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapals', function (Blueprint $table) {
            // Revert to string
            $table->string('nama_kapal')->nullable()->change();
        });
    }
};
