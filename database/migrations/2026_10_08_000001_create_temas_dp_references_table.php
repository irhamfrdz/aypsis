<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temas_dp_references', function (Blueprint $table) {
            $table->foreignId('settlement_stage_id')->constrained('biaya_kapal_temas_stages')->cascadeOnDelete();
            $table->foreignId('dp_stage_id')->constrained('biaya_kapal_temas_stages')->restrictOnDelete();
            $table->primary(['settlement_stage_id', 'dp_stage_id']);
        });
        DB::table('temas_dp_references')->insertUsing(
            ['settlement_stage_id', 'dp_stage_id'],
            DB::table('biaya_kapal_temas_stages')->select('id', 'dp_stage_id')->whereNotNull('dp_stage_id')
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('temas_dp_references');
    }
};
