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
        Schema::create('pembayaran_pranota_kontainer', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran')->unique();
            $table->integer('nomor_cetakan')->default(1);
            $table->string('bank');
            $table->enum('jenis_transaksi', ['transfer', 'tunai', 'cek', 'giro']);
            $table->date('tanggal_kas');
            $table->date('tanggal_pembayaran')->default(now());
            $table->decimal('total_pembayaran', 15, 2);
            $table->decimal('penyesuaian', 15, 2)->default(0);
            $table->decimal('total_setelah_penyesuaian', 15, 2);
            $table->text('alasan_penyesuaian')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('dibuat_oleh');
            $table->unsignedBigInteger('disetujui_oleh')->nullable();
            $table->timestamp('tanggal_persetujuan')->nullable();
            $table->timestamps();

            $table->foreign('dibuat_oleh')->references('id')->on('users');
            $table->foreign('disetujui_oleh')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_kontainer');
    }
};
