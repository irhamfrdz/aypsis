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
            $table->enum('kegiatan', ['BATAL MUAT', 'CHANGE VASSEL', 'DELIVERY', 'DISCHARGE', 'DISCHARGE TL', 'LOADING', 'PENUMPUKAN BPRP', 'PERPANJANGAN DELIVERY', 'RECEIVING', 'RECEIVING LOSING'])
                  ->after('pelabuhan')
                  ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gate_ins', function (Blueprint $table) {
            $table->dropColumn('kegiatan');
        });
    }
};
