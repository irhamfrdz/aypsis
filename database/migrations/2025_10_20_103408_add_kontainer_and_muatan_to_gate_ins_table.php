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
        Schema::table('gate_ins', function (Blueprint $table) {
            $table->enum('kontainer', ['20', '40'])
                  ->after('gudang')
                  ->nullable();
            $table->enum('muatan', ['EMPTY', 'FULL'])
                  ->after('kontainer')
                  ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gate_ins', function (Blueprint $table) {
            $table->dropColumn(['kontainer', 'muatan']);
        });
    }
};
