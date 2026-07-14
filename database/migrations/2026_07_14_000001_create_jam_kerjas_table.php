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
        if (!Schema::hasTable('jam_kerjas')) {
            Schema::create('jam_kerjas', function (Blueprint $table) {
                $table->id();
                $table->string('nama_shift');
                $table->time('jam_masuk');
                $table->time('jam_keluar');
                $table->integer('toleransi_keterlambatan')->default(0);
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
        Schema::dropIfExists('jam_kerjas');
    }
};
