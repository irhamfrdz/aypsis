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
        if (!Schema::hasTable('pembayaran_pranota_uang_jalan_bongkarans')) {
            Schema::create('pembayaran_pranota_uang_jalan_bongkarans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pranota_uang_jalan_bongkaran_id');
            $table->string('nomor_pembayaran')->unique();
            $table->string('nomor_cetakan')->nullable();
            $table->date('tanggal_pembayaran');
            $table->string('bank')->nullable();
            $table->enum('jenis_transaksi', ['cash', 'transfer', 'check', 'giro'])->default('cash');
            $table->decimal('total_pembayaran', 15, 2);
            $table->decimal('total_tagihan_penyesuaian', 15, 2)->default(0);
            $table->decimal('total_tagihan_setelah_penyesuaian', 15, 2);
            $table->text('alasan_penyesuaian')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status_pembayaran', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->string('bukti_pembayaran')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys & Indexes
            $table->foreign('pranota_uang_jalan_bongkaran_id', 'fk_pemb_pranota_bongkaran_pranota')
                ->references('id')
                ->on('pranota_uang_jalan_bongkarans')
                ->onDelete('cascade');
            $table->foreign('created_by', 'fk_pemb_pranota_bongkaran_created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by', 'fk_pemb_pranota_bongkaran_updated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['status_pembayaran', 'tanggal_pembayaran'], 'idx_status_tanggal_bongkaran');
            $table->index(['jenis_transaksi'], 'idx_jenis_transaksi_bongkaran');
            $table->index(['created_at'], 'idx_created_at_bongkaran');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_uang_jalan_bongkarans');
    }
};
