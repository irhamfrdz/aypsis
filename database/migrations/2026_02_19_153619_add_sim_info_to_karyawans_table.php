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
        Schema::table('karyawans', function (Blueprint $table) {
            $table->string('no_sim')->nullable()->after('no_ketenagakerjaan');
            $table->date('sim_berlaku_mulai')->nullable()->after('no_sim');
            $table->date('sim_berlaku_sampai')->nullable()->after('sim_berlaku_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropColumn(['no_sim', 'sim_berlaku_mulai', 'sim_berlaku_sampai']);
        });
    }
};
