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
        Schema::create('pranota_tagihan_kontainer_sewa', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice')->unique();
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->enum('status', ['unpaid', 'paid', 'cancelled'])->default('unpaid');
            $table->json('tagihan_kontainer_sewa_ids')->nullable(); // Array of daftar_tagihan_kontainer_sewa IDs
            $table->integer('jumlah_tagihan')->default(0);
            $table->date('tanggal_pranota');
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pranota_tagihan_kontainer_sewa');
    }
};
