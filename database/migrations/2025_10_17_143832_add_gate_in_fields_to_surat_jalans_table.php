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
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->unsignedBigInteger('gate_in_id')->nullable()->after('status');
            $table->enum('status_gate_in', ['pending', 'proses', 'selesai'])->default('pending')->after('gate_in_id');
            $table->timestamp('tanggal_gate_in')->nullable()->after('status_gate_in');
            $table->text('catatan_gate_in')->nullable()->after('tanggal_gate_in');

            // Add foreign key constraint
            $table->foreign('gate_in_id')->references('id')->on('gate_ins')->onDelete('set null');

            // Add index for better performance
            $table->index(['status_gate_in', 'gate_in_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->dropForeign(['gate_in_id']);
            $table->dropIndex(['status_gate_in', 'gate_in_id']);
            $table->dropColumn(['gate_in_id', 'status_gate_in', 'tanggal_gate_in', 'catatan_gate_in']);
        });
    }
};
