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
        Schema::table('pranota_surat_jalans', function (Blueprint $table) {
            $table->dropColumn('status_pranota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pranota_surat_jalans', function (Blueprint $table) {
            $table->enum('status_pranota', ['pending', 'paid', 'cancelled'])
                  ->default('pending')
                  ->after('total_amount');
        });
    }
};
