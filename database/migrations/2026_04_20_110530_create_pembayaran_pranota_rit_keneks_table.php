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
        Schema::create('pembayaran_pranota_rit_keneks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran')->unique();
            $table->string('nomor_accurate')->nullable();
            $table->date('tanggal_pembayaran');
            $table->string('bank');
            $table->string('jenis_transaksi'); // Debit/Kredit
            $table->decimal('total_pembayaran', 15, 2);
            $table->decimal('total_tagihan_penyesuaian', 15, 2)->default(0);
            $table->decimal('total_tagihan_setelah_penyesuaian', 15, 2);
            $table->text('alasan_penyesuaian')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('status_pembayaran')->default('paid');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('pembayaran_pranota_rit_kenek_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembayaran_pranota_rit_kenek_id')->constrained('pembayaran_pranota_rit_keneks', 'id')->onDelete('cascade')->name('fk_p_rit_kenek_pay');
            $table->foreignId('pranota_uang_rit_kenek_id')->constrained('pranota_uang_rit_keneks')->onDelete('cascade')->name('fk_p_rit_kenek_pran');
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_rit_kenek_items');
        Schema::dropIfExists('pembayaran_pranota_rit_keneks');
    }
};
