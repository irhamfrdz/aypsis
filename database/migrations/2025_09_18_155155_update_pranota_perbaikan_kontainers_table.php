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
        Schema::table('pranota_perbaikan_kontainers', function (Blueprint $table) {
            // Drop estimasi_waktu column
            $table->dropColumn('estimasi_waktu');

            // Rename estimasi_biaya to total_biaya
            $table->renameColumn('estimasi_biaya', 'total_biaya');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_perbaikan_kontainers', function (Blueprint $table) {
            // Rename total_biaya back to estimasi_biaya
            $table->renameColumn('total_biaya', 'estimasi_biaya');

            // Add back estimasi_waktu column
            $table->integer('estimasi_waktu')->comment('dalam jam');
        });
    }
};
