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
        Schema::table('cek_kendaraans', function (Blueprint $table) {
            $table->string('nomor_sim')->nullable()->after('masa_berlaku_kir');
            $table->date('masa_berlaku_sim_start')->nullable()->after('nomor_sim');
            $table->date('masa_berlaku_sim_end')->nullable()->after('masa_berlaku_sim_start');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cek_kendaraans', function (Blueprint $table) {
            $table->dropColumn(['nomor_sim', 'masa_berlaku_sim_start', 'masa_berlaku_sim_end']);
        });
    }
};
