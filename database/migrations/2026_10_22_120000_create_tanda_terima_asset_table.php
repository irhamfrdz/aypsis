<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tanda_terima_asset', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->unique()->constrained('assets')->restrictOnDelete();
            $table->foreignId('karyawan_id')->nullable()->constrained('karyawans')->nullOnDelete();
            $table->string('kode_asset', 50);
            $table->string('nama_asset');
            $table->string('nama_merk', 150)->nullable();
            $table->string('kategori', 100);
            $table->string('nomor_seri', 100)->nullable();
            $table->string('kondisi', 50);
            $table->string('status', 50);
            $table->string('lokasi', 150)->nullable();
            $table->string('nama_pemegang')->nullable();
            $table->string('nik_pemegang')->nullable();
            $table->string('departemen_pemegang')->nullable();
            $table->date('tanggal_tanda_terima')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tanda_terima_asset');
    }
};
