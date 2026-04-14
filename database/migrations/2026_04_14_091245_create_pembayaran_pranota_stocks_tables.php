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
        Schema::create('pembayaran_pranota_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran')->unique();
            $table->string('nomor_accurate')->nullable();
            $table->string('nomor_cetakan')->nullable();
            $table->date('tanggal_pembayaran');
            $table->string('bank')->nullable();
            $table->string('jenis_transaksi')->nullable(); // Debit/Kredit
            $table->decimal('total_pembayaran', 15, 2);
            $table->decimal('total_tagihan_penyesuaian', 15, 2)->default(0);
            $table->decimal('total_tagihan_setelah_penyesuaian', 15, 2);
            $table->text('alasan_penyesuaian')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('status_pembayaran')->default('paid');
            $table->string('bukti_pembayaran')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pembayaran_pranota_stock_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_pranota_stock_id')
                  ->constrained('pembayaran_pranota_stocks')
                  ->onDelete('cascade')
                  ->name('fk_pemb_stock_id');
            $table->foreignId('pranota_stock_id')
                  ->constrained('pranota_stocks')
                  ->onDelete('cascade')
                  ->name('fk_pranota_stock_id');
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_stock_items');
        Schema::dropIfExists('pembayaran_pranota_stocks');
    }
};
