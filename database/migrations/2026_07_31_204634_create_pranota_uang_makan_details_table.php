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
        Schema::create('pranota_uang_makan_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pranota_uang_makan_id')->constrained('pranota_uang_makans')->onDelete('cascade');
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('cascade');
            $table->string('kehadiran')->nullable();
            $table->bigInteger('nominal_awal')->default(0);
            $table->bigInteger('adjustment')->default(0);
            $table->bigInteger('total_akhir')->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_uang_makan_details');
    }
};
