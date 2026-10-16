<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gps_daily_distances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mobil_id')->constrained('mobils')->cascadeOnDelete();
            $table->date('date');
            $table->decimal('distance_km', 12, 3)->default(0);
            $table->unsignedInteger('point_count')->default(0);
            $table->timestamps();

            $table->unique(['mobil_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gps_daily_distances');
    }
};
