<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['temas' => 'meratus', 'meratus' => 'temas'] as $source => $target) {
            Schema::create("{$source}_dp_{$target}_settlements", function (Blueprint $table) use ($source, $target) {
                $table->foreignId('dp_stage_id')->constrained("biaya_kapal_{$source}_stages")->restrictOnDelete();
                $table->foreignId('settlement_stage_id')->constrained("biaya_kapal_{$target}_stages")->cascadeOnDelete();
                $table->decimal('nominal_digunakan', 15, 2)->default(0);
                $table->primary(['settlement_stage_id', 'dp_stage_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('temas_dp_meratus_settlements');
        Schema::dropIfExists('meratus_dp_temas_settlements');
    }
};
