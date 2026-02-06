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
        Schema::create('invoice_kanisir_bans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_invoice')->unique();
            $table->date('tanggal_invoice');
            $table->string('vendor'); // Nama vendor atau ID jika referensi ke tabel vendor
            $table->decimal('total_biaya', 15, 2)->default(0);
            $table->integer('jumlah_ban')->default(0);
            $table->text('keterangan')->nullable();
            $table->string('status')->default('pending'); // pending, paid
            $table->timestamps();
        });

        // Optional: Create items table to link stock_bans to this invoice
        Schema::create('invoice_kanisir_ban_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_kanisir_ban_id')->constrained('invoice_kanisir_bans')->onDelete('cascade');
            $table->foreignId('stock_ban_id')->constrained('stock_bans')->onDelete('cascade');
            $table->decimal('harga', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_kanisir_ban_items');
        Schema::dropIfExists('invoice_kanisir_bans');
    }
};
