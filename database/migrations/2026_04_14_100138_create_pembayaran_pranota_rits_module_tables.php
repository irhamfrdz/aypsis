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
        Schema::create('pembayaran_pranota_rits', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pembayaran')->unique();
            $table->string('nomor_accurate')->nullable();
            $table->date('tanggal_pembayaran');
            $table->string('bank');
            $table->enum('jenis_transaksi', ['Debit', 'Kredit'])->default('Kredit');
            $table->decimal('total_pembayaran', 16, 2)->default(0);
            $table->decimal('total_tagihan_penyesuaian', 16, 2)->default(0);
            $table->decimal('total_tagihan_setelah_penyesuaian', 16, 2)->default(0);
            $table->text('alasan_penyesuaian')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('status_pembayaran')->default('paid');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('pembayaran_pranota_rit_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembayaran_pranota_rit_id');
            $table->unsignedBigInteger('pranota_uang_rit_id');
            $table->decimal('subtotal', 16, 2)->default(0);
            $table->timestamps();

            $table->foreign('pembayaran_pranota_rit_id', 'ppr_id_foreign')
                  ->references('id')->on('pembayaran_pranota_rits')
                  ->onDelete('cascade');
            $table->foreign('pranota_uang_rit_id', 'pur_id_foreign')
                  ->references('id')->on('pranota_uang_rits')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_rit_items');
        Schema::dropIfExists('pembayaran_pranota_rits');
    }
};
