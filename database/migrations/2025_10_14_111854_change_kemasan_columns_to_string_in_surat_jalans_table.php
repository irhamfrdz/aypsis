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
            // Change columns from integer to string for kemasan status
            $table->string('karton')->nullable()->change();
            $table->string('plastik')->nullable()->change();
            $table->string('terpal')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalans', function (Blueprint $table) {
            // Revert back to integer
            $table->integer('karton')->nullable()->change();
            $table->integer('plastik')->nullable()->change();
            $table->integer('terpal')->nullable()->change();
        });
    }
};
