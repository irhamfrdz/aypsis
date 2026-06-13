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
        Schema::create('pembelian_bbm_batams', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_bukti')->unique();
            $table->date('tanggal');
            $table->decimal('jumlah_liter', 10, 2);
            $table->decimal('harga_per_liter', 15, 2);
            $table->decimal('total_harga', 15, 2);
            $table->string('supplier')->nullable();
            $table->string('nomor_nota')->nullable();
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelian_bbm_batams');
    }
};
