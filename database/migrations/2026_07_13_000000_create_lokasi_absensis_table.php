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
        if (!Schema::hasTable('lokasi_absensis')) {
            Schema::create('lokasi_absensis', function (Blueprint $table) {
                $table->id();
                $table->string('nama_lokasi');
                $table->double('latitude');
                $table->double('longitude');
                $table->integer('radius')->default(100);
                $table->text('keterangan')->nullable();
                $table->tinyInteger('is_active')->default(1);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lokasi_absensis');
    }
};
