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
        Schema::create('tagihan_cat', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_kontainer');
            $table->string('vendor')->nullable();
            $table->date('tanggal_tagihan');
            $table->decimal('jumlah', 15, 2);
            $table->string('status')->default('pending'); // pending, paid, cancelled
            $table->text('keterangan')->nullable();
            $table->foreignId('perbaikan_kontainer_id')->nullable()->constrained('perbaikan_kontainers')->onDelete('set null');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Indexes
            $table->index(['nomor_kontainer']);
            $table->index(['vendor']);
            $table->index(['status']);
            $table->index(['tanggal_tagihan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_cat');
    }
};
