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
        Schema::create('gate_ins', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_gate_in', 20)->unique();
            $table->foreignId('terminal_id')->constrained('master_terminals');
            $table->foreignId('kapal_id')->constrained('master_kapals');
            $table->foreignId('service_id')->constrained('master_services');
            $table->datetime('tanggal_gate_in');
            $table->foreignId('user_id')->constrained('users');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'tanggal_gate_in']);
            $table->index(['nomor_gate_in']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gate_ins');
    }
};
