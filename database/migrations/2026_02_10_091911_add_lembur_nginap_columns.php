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
            $table->boolean('lembur')->default(false)->nullable();
            $table->boolean('nginap')->default(false)->nullable();
        });

        Schema::table('tanda_terimas', function (Blueprint $table) {
            $table->boolean('lembur')->default(false)->nullable();
            $table->boolean('nginap')->default(false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->dropColumn(['lembur', 'nginap']);
        });

        Schema::table('tanda_terimas', function (Blueprint $table) {
            $table->dropColumn(['lembur', 'nginap']);
        });
    }
};

