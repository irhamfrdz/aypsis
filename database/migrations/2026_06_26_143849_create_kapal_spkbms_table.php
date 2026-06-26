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
        Schema::create('kapal_spkbms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kapal_id')->constrained('master_kapals')->onDelete('cascade');
            $table->string('nomor_surat')->unique();
            $table->string('hal');
            $table->text('ditujukan_kepada');
            $table->string('voyage');
            $table->string('rencana_tiba');
            $table->string('rencana_sandar');
            $table->text('rencana_bongkar');
            $table->text('rencana_muat');
            $table->string('tujuan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kapal_spkbms');
    }
};
