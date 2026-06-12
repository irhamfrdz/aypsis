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
        Schema::table('biaya_kapal_temas', function (Blueprint $table) {
            $table->string('nomor_kontainer')->nullable()->after('voyage');
            $table->unsignedBigInteger('bl_id')->nullable()->after('nomor_kontainer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biaya_kapal_temas', function (Blueprint $table) {
            $table->dropColumn(['nomor_kontainer', 'bl_id']);
        });
    }
};
