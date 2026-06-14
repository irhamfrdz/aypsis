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
        Schema::create('master_kartu_bensin_batams', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kartu')->unique();
            $table->string('nama_kartu');
            $table->string('provider')->default('Pertamina');
            $table->unsignedBigInteger('mobil_id')->nullable();
            $table->unsignedBigInteger('karyawan_id')->nullable();
            $table->string('status')->default('aktif'); // 'aktif', 'tidak_aktif'
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('mobil_id')->references('id')->on('mobils')->onDelete('set null');
            $table->foreign('karyawan_id')->references('id')->on('karyawans')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_kartu_bensin_batams');
    }
};
