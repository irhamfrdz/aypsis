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
        // Drop status column from tanda_terimas table
        Schema::table('tanda_terimas', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // Drop status column from tanda_terima_tanpa_surat_jalan table
        Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add status column to tanda_terimas table
        Schema::table('tanda_terimas', function (Blueprint $table) {
            $table->enum('status', ['draft', 'submitted', 'approved', 'completed', 'cancelled'])
                  ->default('draft')
                  ->after('catatan');
        });

        // Re-add status column to tanda_terima_tanpa_surat_jalan table
        Schema::table('tanda_terima_tanpa_surat_jalan', function (Blueprint $table) {
            $table->enum('status', ['draft', 'submitted', 'approved', 'completed', 'cancelled'])
                  ->default('draft')
                  ->after('catatan');
        });
    }
};
