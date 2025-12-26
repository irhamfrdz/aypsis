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
        Schema::table('naik_kapal', function (Blueprint $table) {
            $table->boolean('is_tl')->default(false)->after('sudah_ob')->comment('Apakah kontainer ini hasil TL (Tanda Langsung)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('naik_kapal', function (Blueprint $table) {
            $table->dropColumn('is_tl');
        });
    }
};
