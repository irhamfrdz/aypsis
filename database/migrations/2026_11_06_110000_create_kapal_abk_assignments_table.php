<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kapal_abk_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kapal_id')->constrained('master_kapals')->cascadeOnDelete();
            $table->foreignId('karyawan_id')->unique()->constrained('karyawans')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kapal_abk_assignments');
    }
};
