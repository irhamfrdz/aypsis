<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apd_return_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->unsignedBigInteger('transaction_id');
            $table->string('nomor_transaksi', 100);
            $table->unsignedBigInteger('karyawan_id')->nullable();
            $table->string('nama_karyawan')->nullable();
            $table->string('nik')->nullable();
            $table->string('nama_barang');
            $table->string('ukuran', 100)->nullable();
            $table->decimal('qty', 12, 2);
            $table->unsignedBigInteger('returned_by')->nullable();
            $table->string('nama_pengguna')->nullable();
            $table->timestamp('returned_at');
            $table->unique('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apd_return_histories');
    }
};
