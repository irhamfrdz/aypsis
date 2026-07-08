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
        Schema::create('mesins', function (Blueprint $table) {
            $table->id();
            $table->string('kode_mesin')->unique();
            $table->string('nama_mesin');
            $table->string('tipe_mesin');
            $table->string('ip_address')->nullable();
            $table->integer('port')->default(4370);
            $table->string('comm_key')->nullable();
            $table->string('status')->default('Aktif'); // e.g. Aktif, Rusak, Perbaikan, Nonaktif
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mesins');
    }
};
