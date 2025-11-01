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
        Schema::create('pembayaran_uang_rits', function (Blueprint $table) {
            $table->id();
            $table->string('no_pembayaran')->unique(); // Auto-generated payment number
            $table->date('tanggal_pembayaran'); // Payment date
            $table->string('nama_supir'); // Driver name
            $table->string('no_plat'); // License plate
            $table->integer('total_uang_jalan')->default(0); // Total road money
            $table->integer('total_uang_rit')->default(0); // Total trip money
            $table->integer('total_pembayaran')->default(0); // Total payment amount
            $table->text('keterangan')->nullable(); // Notes
            $table->enum('status', ['draft', 'paid'])->default('draft'); // Payment status
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('paid_by')->nullable(); // Who marked as paid
            $table->timestamp('paid_at')->nullable(); // When marked as paid
            $table->timestamps();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('paid_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_uang_rits');
    }
};
