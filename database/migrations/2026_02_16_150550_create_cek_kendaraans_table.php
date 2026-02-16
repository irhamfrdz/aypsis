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
        Schema::create('cek_kendaraans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('cascade');
            $table->foreignId('mobil_id')->constrained('mobils')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam');
            
            // Checklist Items
            $table->string('oli_mesin')->default('baik'); // baik, perlu_cek, buruk
            $table->string('air_radiator')->default('baik');
            $table->string('minyak_rem')->default('baik');
            $table->string('air_wiper')->default('baik');
            $table->string('lampu_depan')->default('baik');
            $table->string('lampu_belakang')->default('baik');
            $table->string('lampu_sein')->default('baik');
            $table->string('lampu_rem')->default('baik');
            $table->string('kondisi_ban')->default('baik');
            $table->string('tekanan_ban')->default('baik');
            $table->string('aki')->default('baik');
            $table->string('fungsi_rem')->default('baik');
            $table->string('fungsi_kopling')->default('baik');
            $table->string('kebersihan_interior')->default('baik');
            $table->string('kebersihan_eksterior')->default('baik');
            
            $table->integer('bahan_bakar')->default(0); // percecntage or level
            $table->integer('odometer')->nullable();
            
            $table->text('catatan')->nullable();
            $table->string('foto_sebelum')->nullable();
            $table->string('foto_sesudah')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cek_kendaraans');
    }
};
