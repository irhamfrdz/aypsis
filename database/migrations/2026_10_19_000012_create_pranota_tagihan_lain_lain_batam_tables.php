<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pranota_tagihan_lain_lain_batams', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota', 40)->unique();
            $table->date('tanggal_pranota');
            $table->text('keterangan')->nullable();
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('pranota_tagihan_lain_lain_batam_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pranota_id')->constrained('pranota_tagihan_lain_lain_batams')->cascadeOnDelete();
            $table->foreignId('tagihan_id')->constrained('tagihan_lain_lain_batams')->restrictOnDelete();
            $table->decimal('total_biaya', 15, 2);
            $table->timestamps();
            $table->unique('tagihan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pranota_tagihan_lain_lain_batam_items');
        Schema::dropIfExists('pranota_tagihan_lain_lain_batams');
    }
};
