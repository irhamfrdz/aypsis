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
            $table->string('lampu_sein_depan_kiri')->default('berfungsi');
            $table->string('lampu_sein_belakang_kanan')->default('berfungsi');
            $table->string('lampu_sein_belakang_kiri')->default('berfungsi');
            $table->string('lampu_rem_kanan')->default('berfungsi');
            $table->string('lampu_rem_kiri')->default('berfungsi');
            $table->string('lampu_mundur_kanan')->default('berfungsi');
            $table->string('lampu_mundur_kiri')->default('berfungsi');
            $table->string('sabuk_pengaman_kanan')->default('berfungsi');
            $table->string('sabuk_pengaman_kiri')->default('berfungsi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cek_kendaraans', function (Blueprint $table) {
            //
        });
    }
};
