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
        Schema::create('biaya_kapal_freights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biaya_kapal_id')->constrained('biaya_kapals')->onDelete('cascade');
            $table->string('kapal')->nullable();
            $table->string('voyage')->nullable();
            $table->string('vendor')->nullable();
            $table->json('kontainer_ids')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('biaya_meterai', 15, 2)->default(0);
            $table->decimal('pph', 15, 2)->default(0);
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_kapal_freights');
    }
};
