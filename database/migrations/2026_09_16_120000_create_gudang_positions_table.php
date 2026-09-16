<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gudangs', function (Blueprint $table) {
            $table->json('denah_layout')->nullable();
            $table->unsignedInteger('denah_version')->default(0);
        });

        Schema::create('gudang_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gudang_id')->constrained('gudangs')->cascadeOnDelete();
            $table->string('source', 10);
            $table->unsignedBigInteger('container_id');
            $table->string('container_number');
            $table->unsignedTinyInteger('span');
            $table->string('block', 12);
            $table->unsignedSmallInteger('bay');
            $table->unsignedSmallInteger('row');
            $table->unsignedTinyInteger('tier');
            $table->timestamps();
            $table->unique(['gudang_id', 'source', 'container_id'], 'gudang_position_container_unique');
            $table->unique(['gudang_id', 'block', 'bay', 'row', 'tier'], 'gudang_position_slot_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gudang_positions');
        Schema::table('gudangs', function (Blueprint $table) {
            $table->dropColumn(['denah_layout', 'denah_version']);
        });
    }
};
