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
        Schema::create('uang_lembur_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uang_lembur_id')->constrained('master_uang_lemburs')->onDelete('cascade');
            $table->string('tipe_hari'); // Hari Biasa / Hari Libur
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->boolean('is_sampai_selesai')->default(false);
            $table->string('satuan')->default('Hari');
            $table->decimal('nominal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uang_lembur_rules');
    }
};
