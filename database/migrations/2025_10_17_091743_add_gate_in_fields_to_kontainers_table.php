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
        Schema::table('kontainers', function (Blueprint $table) {
            $table->foreignId('gate_in_id')->nullable()->constrained('gate_ins');
            $table->enum('status_checkpoint_supir', ['pending', 'proses', 'selesai'])->default('pending');
            $table->datetime('tanggal_checkpoint_supir')->nullable();
            $table->enum('status_gate_in', ['pending', 'proses', 'selesai'])->default('pending');
            $table->datetime('tanggal_gate_in')->nullable();
            $table->foreignId('terminal_id')->nullable()->constrained('master_terminals');
            $table->foreignId('service_id')->nullable()->constrained('master_services');

            $table->index(['status_checkpoint_supir']);
            $table->index(['status_gate_in']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontainers', function (Blueprint $table) {
            $table->dropForeign(['gate_in_id']);
            $table->dropForeign(['terminal_id']);
            $table->dropForeign(['service_id']);
            $table->dropColumn([
                'gate_in_id',
                'status_checkpoint_supir',
                'tanggal_checkpoint_supir',
                'status_gate_in',
                'tanggal_gate_in',
                'terminal_id',
                'service_id'
            ]);
        });
    }
};
