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
        Schema::create('biaya_kapal_stuffing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('biaya_kapal_id');
            $table->string('kapal')->nullable();
            $table->string('voyage')->nullable();
            $table->json('tanda_terima_ids')->nullable();
            $table->timestamps();

            $table->foreign('biaya_kapal_id')->references('id')->on('biaya_kapals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_kapal_stuffing');
    }
};
