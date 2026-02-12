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
        Schema::create('pranota_lemburs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pranota')->unique();
            $table->integer('nomor_cetakan')->default(1);
            $table->date('tanggal_pranota');
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->decimal('adjustment', 15, 2)->default(0);
            $table->text('alasan_adjustment')->nullable();
            $table->decimal('total_setelah_adjustment', 15, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'paid', 'cancelled'])->default('draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });

        // Pivot table for pranota lembur and surat jalans
        Schema::create('pranota_lembur_surat_jalan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pranota_lembur_id');
            $table->unsignedBigInteger('surat_jalan_id')->nullable();
            $table->unsignedBigInteger('surat_jalan_bongkaran_id')->nullable();
            $table->string('supir');
            $table->string('no_plat');
            $table->boolean('is_lembur')->default(false);
            $table->boolean('is_nginap')->default(false);
            $table->decimal('biaya_lembur', 15, 2)->default(0);
            $table->decimal('biaya_nginap', 15, 2)->default(0);
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('pranota_lembur_id')->references('id')->on('pranota_lemburs')->onDelete('cascade');
            $table->foreign('surat_jalan_id')->references('id')->on('surat_jalans')->onDelete('cascade');
            $table->foreign('surat_jalan_bongkaran_id')->references('id')->on('surat_jalan_bongkarans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_lembur_surat_jalan');
        Schema::dropIfExists('pranota_lemburs');
    }
};
