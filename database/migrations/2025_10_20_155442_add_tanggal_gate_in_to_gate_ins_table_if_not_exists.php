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
        Schema::table('gate_ins', function (Blueprint $table) {
            // Check if tanggal_gate_in column doesn't exist, then add it
            if (!Schema::hasColumn('gate_ins', 'tanggal_gate_in')) {
                $table->timestamp('tanggal_gate_in')->nullable()->after('kapal_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gate_ins', function (Blueprint $table) {
            if (Schema::hasColumn('gate_ins', 'tanggal_gate_in')) {
                $table->dropColumn('tanggal_gate_in');
            }
        });
    }
};
