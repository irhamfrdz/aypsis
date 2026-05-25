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
            $table->string('nomor_pranota')->unique();
            $table->date('tanggal_pranota');
            $table->string('nomor_accurate')->nullable();
            $table->string('vendor')->nullable();
            $table->string('bank')->nullable();
            $table->string('rekening')->nullable();
            $table->string('penerima')->nullable();
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->decimal('adjustment', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->json('items'); // Snapshot perbaikan kontainer items
            $table->string('status')->default('approved');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
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
