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
        Schema::create('pembayaran_pranota_vendor_supirs', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran')->unique();
            $table->date('tanggal_pembayaran');
            $table->foreignId('vendor_id')->constrained('vendor_supirs')->onDelete('cascade');
            $table->decimal('total_pembayaran', 15, 2);
            $table->string('metode_pembayaran')->default('transfer');
            $table->string('bank')->nullable();
            $table->string('no_referensi')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_vendor_supirs');
    }
};
