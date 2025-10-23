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
        Schema::dropIfExists('prospek');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('prospek', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->comment('Tanggal tanda terima kontainer');
            $table->string('nama_supir')->nullable()->comment('Nama supir yang mengantarkan kontainer');
            $table->string('barang')->nullable()->comment('Jenis barang/tipe kontainer');
            $table->string('pt_pengirim')->nullable()->comment('PT/Perusahaan pengirim');
            $table->enum('ukuran', ['20', '40'])->nullable()->comment('Ukuran kontainer dalam feet');
            $table->string('nomor_kontainer')->nullable()->comment('Nomor identifikasi kontainer');
            $table->string('no_seal')->nullable()->comment('Nomor seal kontainer');
            $table->string('tujuan_pengiriman')->nullable()->comment('Tujuan pengiriman kontainer');
            $table->string('nama_kapal')->nullable()->comment('Estimasi nama kapal');
            $table->text('keterangan')->nullable()->comment('Catatan tambahan');
            $table->enum('status', ['aktif', 'sudah_muat', 'batal'])->default('aktif')->comment('Status prospek kontainer');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            // Add indexes for better performance
            $table->index(['tanggal', 'status']);
            $table->index('ukuran');
            $table->index('tujuan_pengiriman');
            $table->index('status');

            // Foreign key constraints (optional)
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }
};
