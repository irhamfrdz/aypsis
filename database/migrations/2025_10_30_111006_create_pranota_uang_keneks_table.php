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
        Schema::create('pranota_uang_keneks', function (Blueprint $table) {
            $table->id();
            $table->string('no_pranota')->unique();
            $table->date('tanggal');
            $table->unsignedBigInteger('surat_jalan_id');
            $table->string('no_surat_jalan');
            $table->string('supir_nama');
            $table->string('kenek_nama');
            $table->string('no_plat');
            $table->decimal('uang_rit_kenek', 15, 2)->default(50000); // Uang rit kenek
            $table->decimal('total_rit', 15, 2)->default(0); // Total yang sama dengan uang_rit_kenek
            $table->decimal('total_uang', 15, 2)->default(0); // Total keseluruhan
            $table->text('keterangan')->nullable();
            $table->enum('status', ['draft', 'submitted', 'approved', 'paid', 'cancelled'])->default('draft');
            $table->date('tanggal_bayar')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('surat_jalan_id')->references('id')->on('surat_jalans')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');

            // Indexes
            $table->index('tanggal');
            $table->index('status');
            $table->index('surat_jalan_id');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_uang_keneks');
    }
};
