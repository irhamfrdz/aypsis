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
        Schema::create('pelamar_karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('wearpack_size')->nullable();
            $table->string('no_safety_shoes')->nullable();
            $table->string('nomor_rekening')->nullable();
            $table->string('npwp')->nullable();
            $table->string('no_nik')->unique();
            $table->string('no_kartu_keluarga')->nullable();
            $table->string('no_bpjs_kesehatan')->nullable();
            $table->string('no_ketenagakerjaan')->nullable();
            $table->string('jenis_kelamin');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('no_handphone');
            $table->integer('tanggungan_anak')->default(0);
            $table->text('alamat_lengkap');
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kota_kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('email')->nullable();
            $table->string('kontak_darurat')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelamar_karyawans');
    }
};
