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
        Schema::table('mobils', function (Blueprint $table) {
            // Rename existing jatuh_tempo_asuransi to tanggal_jatuh_tempo_asuransi
            $table->renameColumn('jatuh_tempo_asuransi', 'tanggal_jatuh_tempo_asuransi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mobils', function (Blueprint $table) {
            $table->renameColumn('tanggal_jatuh_tempo_asuransi', 'jatuh_tempo_asuransi');
        });
    }
};
