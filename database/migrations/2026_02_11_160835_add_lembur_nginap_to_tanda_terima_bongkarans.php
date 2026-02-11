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
        Schema::table('tanda_terima_bongkarans', function (Blueprint $table) {
            $table->boolean('lembur')->default(false)->nullable();
            $table->boolean('nginap')->default(false)->nullable();
            $table->boolean('tidak_lembur_nginap')->default(false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tanda_terima_bongkarans', function (Blueprint $table) {
            $table->dropColumn(['lembur', 'nginap', 'tidak_lembur_nginap']);
        });
    }
};
