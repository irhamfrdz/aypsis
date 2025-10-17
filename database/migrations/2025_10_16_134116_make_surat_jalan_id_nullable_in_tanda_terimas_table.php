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
        Schema::table('tanda_terimas', function (Blueprint $table) {
            // Make surat_jalan_id nullable to allow manual entries
            $table->foreignId('surat_jalan_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terimas', function (Blueprint $table) {
            // Revert to not nullable
            $table->foreignId('surat_jalan_id')->nullable(false)->change();
        });
    }
};
