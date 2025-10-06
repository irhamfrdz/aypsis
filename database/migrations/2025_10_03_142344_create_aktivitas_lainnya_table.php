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
        Schema::create('aktivitas_lainnya', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_aktivitas')->unique();
            $table->date('tanggal_aktivitas');
            $table->text('deskripsi_aktivitas');
            $table->string('kategori')->default('lainnya');
            $table->foreignId('vendor_id')->nullable()->constrained('vendor_bengkel')->onDelete('set null');
            $table->decimal('nominal', 15, 2);
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'paid'])->default('draft');
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'kategori']);
            $table->index('tanggal_aktivitas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aktivitas_lainnya');
    }
};
