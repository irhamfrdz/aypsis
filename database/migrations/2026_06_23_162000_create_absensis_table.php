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
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('karyawan_id')->nullable();
            $table->string('nik');
            $table->dateTime('waktu');
            $table->string('tipe'); // Masuk, Pulang
            $table->unsignedBigInteger('mesin_id')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('karyawan_id')->references('id')->on('karyawans')->onDelete('set null');
            $table->foreign('mesin_id')->references('id')->on('mesins')->onDelete('set null');

            // Unique index to prevent duplicate attendance logs synced from machines
            $table->unique(['nik', 'waktu', 'tipe'], 'absensi_unique_scan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
