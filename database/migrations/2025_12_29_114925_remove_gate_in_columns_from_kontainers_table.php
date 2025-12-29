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
            // Drop foreign key first if it exists
            $table->dropForeign(['terminal_id']);
            
            // Then drop the columns
            $table->dropColumn(['status_gate_in', 'tanggal_gate_in', 'terminal_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontainers', function (Blueprint $table) {
            $table->string('status_gate_in')->nullable();
            $table->date('tanggal_gate_in')->nullable();
            $table->unsignedBigInteger('terminal_id')->nullable();
            
            // Restore foreign key
            $table->foreign('terminal_id')->references('id')->on('terminals')->onDelete('set null');
        });
    }
};
