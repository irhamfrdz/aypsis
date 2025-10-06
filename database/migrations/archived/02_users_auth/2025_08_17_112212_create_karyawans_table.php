<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('karyawans', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();
            $table->string('nama_panggilan');
            $table->string('nama_lengkap');
            $table->string('plat')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('ktp')->unique()->nullable();
            $table->string('kk')->nullable();
            $table->string('alamat')->nullable();
            $table->string('rt_rw')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('alamat_lengkap')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('status_perkawinan')->nullable();
            $table->string('agama')->nullable();
            $table->string('divisi')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_berhenti')->nullable();
            $table->string('status_pajak')->nullable();
            $table->string('nama_bank')->nullable();
            $table->string('akun_bank')->nullable();
            $table->string('atas_nama')->nullable();
            $table->string('jkn')->nullable();
            $table->string('cabang')->nullable();
            $table->string('nik_supervisor')->nullable();
            $table->string('supervisor')->nullable();
            $table->timestamps();
        });
    }


      public function down()
    {
        Schema::dropIfExists('karyawans');
    }
};
