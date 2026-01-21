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
        Schema::create('pricelist_tkbms', function (Blueprint $table) {
            $table->id();
            $table->string('cargo')->nullable(); // Column 'cargo' as requested
            $table->decimal('tarif_20f', 15, 2)->default(0); // 20F
            $table->decimal('tarif_40f', 15, 2)->default(0); // 40F
            $table->decimal('tarif_20m', 15, 2)->default(0); // 20M
            $table->decimal('tarif_40m', 15, 2)->default(0); // 40M
            $table->decimal('tuslag', 15, 2)->default(0);
            $table->string('status')->default('active'); // active/inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricelist_tkbms');
    }
};
