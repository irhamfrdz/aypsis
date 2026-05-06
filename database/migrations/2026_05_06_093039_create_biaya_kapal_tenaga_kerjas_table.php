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
        Schema::create('biaya_kapal_tenaga_kerjas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biaya_kapal_id')->constrained('biaya_kapals')->onDelete('cascade');
            $table->foreignId('buruh_id')->constrained('buruhs')->onDelete('cascade');
            $table->decimal('nominal', 15, 2);
            $table->string('kapal')->nullable();
            $table->string('voyage')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_kapal_tenaga_kerjas');
    }
};
