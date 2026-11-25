<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bpjs_effective_values', function (Blueprint $table) {
            $table->id();
            $table->string('target_type');
            $table->unsignedBigInteger('target_id');
            $table->string('component');
            $table->date('valid_from');
            $table->date('valid_until');
            $table->json('values');
            $table->timestamps();
            $table->index(['target_type', 'target_id', 'component'], 'bpjs_effective_target');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bpjs_effective_values');
    }
};
