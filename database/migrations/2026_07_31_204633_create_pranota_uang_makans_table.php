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
        Schema::create('pranota_uang_makans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota')->unique();
            $table->date('tanggal_pranota');
            $table->bigInteger('total_nominal')->default(0);
            $table->string('status')->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_uang_makans');
    }
};
