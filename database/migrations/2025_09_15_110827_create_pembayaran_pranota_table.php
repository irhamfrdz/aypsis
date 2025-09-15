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
        Schema::create('pembayaran_pranota_perbaikan_kontainers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pranota_perbaikan_kontainer_id');
            $table->date('tanggal_pembayaran');
            $table->decimal('nominal_pembayaran', 15, 2);
            $table->string('nomor_invoice')->nullable();
            $table->enum('metode_pembayaran', ['cash', 'transfer', 'check', 'credit_card'])->default('transfer');
            $table->text('keterangan')->nullable();
            $table->enum('status_pembayaran', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();

            // Indexes
            $table->index(['pranota_perbaikan_kontainer_id', 'status_pembayaran']);
            $table->index('tanggal_pembayaran');
            $table->index('status_pembayaran');
        });

        // Add foreign key constraints separately
        Schema::table('pembayaran_pranota_perbaikan_kontainers', function (Blueprint $table) {
            $table->foreign('pranota_perbaikan_kontainer_id', 'fk_pembayaran_pranota')->references('id')->on('pranota_perbaikan_kontainers')->onDelete('cascade');
            $table->foreign('created_by', 'fk_pembayaran_created_by')->references('id')->on('users');
            $table->foreign('updated_by', 'fk_pembayaran_updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_perbaikan_kontainers');
    }
};
