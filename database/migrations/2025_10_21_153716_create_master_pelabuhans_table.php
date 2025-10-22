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
        Schema::create('master_pelabuhans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelabuhan');
            $table->string('kota');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();

            // Indexes for better performance
            $table->index('status');
            $table->index('nama_pelabuhan');
            $table->index('kota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_pelabuhans');
    }
};
