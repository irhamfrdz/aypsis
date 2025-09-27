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
        Schema::create('pranota_perbaikan_kontainers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perbaikan_kontainer_id')->constrained('perbaikan_kontainers')->onDelete('cascade');
            $table->date('tanggal_pranota');
            $table->text('deskripsi_pekerjaan');
            $table->string('nama_teknisi');
            $table->decimal('estimasi_biaya', 15, 2);
            $table->integer('estimasi_waktu')->comment('dalam jam');
            $table->text('catatan')->nullable();
            $table->enum('status', ['draft', 'belum_dibayar', 'approved', 'in_progress', 'completed', 'cancelled'])->default('belum_dibayar');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_perbaikan_kontainers');
    }
};
