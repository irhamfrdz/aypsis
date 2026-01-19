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
        Schema::create('biaya_kapal_air', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biaya_kapal_id')->constrained('biaya_kapals')->onDelete('cascade');
            $table->string('kapal')->nullable();
            $table->string('voyage')->nullable();
            $table->string('vendor')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->string('type_keterangan')->nullable();
            $table->decimal('kuantitas', 15, 2)->default(0);
            $table->decimal('harga', 15, 2)->default(0);
            $table->decimal('jasa_air', 15, 2)->default(0);
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('pph', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->string('penerima')->nullable();
            $table->timestamps();
            
            // Index for faster queries
            $table->index('biaya_kapal_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_kapal_air');
    }
};
