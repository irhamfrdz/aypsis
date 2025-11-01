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
            $table->decimal('volume', 10, 3)->nullable()->after('tonnage')->comment('Volume in cubic meters (m³)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bls', function (Blueprint $table) {
            $table->dropColumn('volume');
        });
    }
};
