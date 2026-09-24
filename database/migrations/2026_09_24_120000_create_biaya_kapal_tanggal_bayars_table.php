<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biaya_kapal_tanggal_bayars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biaya_kapal_id')->constrained('biaya_kapals')->cascadeOnDelete();
            $table->string('kapal');
            $table->string('voyage');
            $table->date('tanggal_bayar')->nullable();
            $table->timestamps();

            $table->unique(['biaya_kapal_id', 'kapal', 'voyage'], 'biaya_kapal_tanggal_bayar_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biaya_kapal_tanggal_bayars');
    }
};
