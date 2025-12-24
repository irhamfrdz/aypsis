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
            // Drop the existing index first
            $table->dropIndex(['no_voyage']);
            // Change column to JSON
            $table->json('no_voyage')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapals', function (Blueprint $table) {
            // Revert to string
            $table->string('no_voyage')->nullable()->change();
            $table->index('no_voyage');
        });
    }
};
