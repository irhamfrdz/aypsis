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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('container_trips');
        Schema::dropIfExists('vendors');
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('nama_vendor');
            $table->enum('tipe_hitung', ['bulanan', 'harian'])->comment('bulanan/harian');
            $table->timestamps();
        });

        Schema::create('container_trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors');
            $table->string('no_kontainer');
            $table->string('ukuran');
            $table->date('tgl_ambil');
            $table->date('tgl_kembali')->nullable();
            $table->decimal('harga_sewa', 15, 2);
            $table->timestamps();
        });
    }
};
