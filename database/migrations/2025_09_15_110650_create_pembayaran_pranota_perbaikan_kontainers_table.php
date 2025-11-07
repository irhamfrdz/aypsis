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
            $table->foreignId('pranota_perbaikan_kontainer_id')->constrained('pranota_perbaikan_kontainers', 'fk_pembayaran_pranota')->onDelete('cascade')->name('fk_pembayaran_pranota_perbaikan_id');
            $table->date('tanggal_pembayaran');
            $table->decimal('nominal_pembayaran', 15, 2);
            $table->string('nomor_invoice')->nullable();
            $table->enum('metode_pembayaran', ['cash', 'transfer', 'check', 'credit_card'])->default('transfer');
            $table->text('keterangan')->nullable();
            $table->enum('status_pembayaran', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->foreignId('created_by')->constrained('users', 'fk_pembayaran_created_by')->name('fk_pembayaran_created_by');
            $table->foreignId('updated_by')->constrained('users', 'fk_pembayaran_updated_by')->name('fk_pembayaran_updated_by');
            $table->timestamps();
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
