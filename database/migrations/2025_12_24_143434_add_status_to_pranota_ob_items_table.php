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
        Schema::table('pranota_ob_items', function (Blueprint $table) {
            $table->string('status', 20)->nullable()->after('biaya')->comment('Status kontainer: full atau empty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_ob_items', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
