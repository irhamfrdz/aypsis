<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pranota_ob_muat_temas', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota')->unique();
            $table->date('tanggal_pranota');
            $table->string('nama_kapal');
            $table->string('no_voyage');
            $table->string('nomor_accurate')->nullable();
            $table->decimal('nominal', 15, 2);
            $table->decimal('adjustment', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2);
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('pranota_ob_muat_temas_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pranota_ob_muat_temas_id')->constrained('pranota_ob_muat_temas')->cascadeOnDelete();
            $table->foreignId('tagihan_ob_id')->unique()->constrained('tagihan_ob')->restrictOnDelete();
            $table->json('snapshot');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pranota_ob_muat_temas_items');
        Schema::dropIfExists('pranota_ob_muat_temas');
    }
};
