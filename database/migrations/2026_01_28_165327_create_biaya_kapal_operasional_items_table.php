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
        Schema::create('biaya_kapal_operasional_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biaya_kapal_operasional_id')->constrained('biaya_kapal_operasionals')->onDelete('cascade');
            $table->string('deskripsi')->nullable();
            $table->decimal('nominal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_kapal_operasional_items');
    }
};
