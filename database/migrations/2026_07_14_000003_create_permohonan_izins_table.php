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
        if (!Schema::hasTable('permohonan_izins')) {
            Schema::create('permohonan_izins', function (Blueprint $table) {
                $table->id();
                $table->integer('karyawan_id')->nullable();
                $table->string('nik');
                $table->string('nama');
                $table->string('divisi');
                $table->string('jenis_izin', 50);
                $table->date('tanggal_mulai');
                $table->date('tanggal_selesai');
                $table->string('waktu', 50)->nullable();
                $table->text('alasan');
                $table->string('lampiran')->nullable();
                $table->string('status', 50)->default('PENDING');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_izins');
    }
};
