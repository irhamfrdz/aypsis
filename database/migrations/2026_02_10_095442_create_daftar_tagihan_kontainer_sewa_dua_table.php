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
        Schema::create('daftar_tagihan_kontainer_sewa_dua', function (Blueprint $table) {
            $table->id();
            $table->string('vendor')->nullable();
            $table->string('nomor_kontainer')->nullable();
            $table->string('size')->nullable();
            $table->date('tanggal_awal')->nullable();
            $table->date('tanggal_akhir')->nullable();
            $table->string('group')->nullable();
            $table->integer('periode')->nullable();
            $table->string('masa')->nullable();
            $table->string('tarif')->nullable();
            $table->string('status')->default('ongoing')->nullable();
            $table->string('status_pembayaran')->default('belum_dibayar')->nullable();
            $table->string('nomor_invoice_vendor')->nullable();

            // Financials
            $table->decimal('dpp', 15, 2)->default(0);
            $table->decimal('adjustment', 15, 2)->default(0);
            $table->text('adjustment_note')->nullable();
            $table->decimal('dpp_nilai_lain', 15, 2)->default(0);
            $table->decimal('ppn', 15, 2)->default(0);
            $table->decimal('pph', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);

            // Additional info
            $table->string('nomor_bank')->nullable();
            $table->string('invoice_vendor')->nullable();
            $table->date('tanggal_vendor')->nullable();

            // Relations (nullable if not strictly required immediately)
            $table->unsignedBigInteger('pranota_id')->nullable();
            $table->string('status_pranota')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();

            // Auditable columns (assuming standard)
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable(); // If soft deletes used
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_tagihan_kontainer_sewa_dua');
    }
};
