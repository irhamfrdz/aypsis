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
        Schema::table('pranota_obs', function (Blueprint $table) {
            $table->decimal('adjustment', 15, 2)->nullable()->after('nomor_accurate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_obs', function (Blueprint $table) {
            $table->dropColumn('adjustment');
        });
    }
};
