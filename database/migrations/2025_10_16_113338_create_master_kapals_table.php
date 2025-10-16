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
        Schema::create('master_kapals', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 50)->unique()->comment('Kode unik kapal');
            $table->string('kode_kapal', 100)->nullable()->comment('Kode alternatif kapal');
            $table->string('nama_kapal', 255)->comment('Nama kapal');
            $table->text('catatan')->nullable()->comment('Catatan tambahan');
            $table->string('lokasi', 255)->nullable()->comment('Lokasi kapal');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->comment('Status kapal');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('kode');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_kapals');
    }
};
