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
        Schema::create('pranota_pumls', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota', 100)->unique();
            $table->date('tanggal_pranota');
            $table->date('periode_start')->nullable();
            $table->date('periode_end')->nullable();
            $table->decimal('total_uang_makan', 15, 2)->default(0);
            $table->decimal('total_lembur', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->string('status', 30)->default('draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_pumls');
    }
};
