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
        Schema::create('invoice_tagihan_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice')->unique();
            $table->unsignedBigInteger('vendor_id');
            $table->date('tanggal_invoice');
            $table->decimal('total_nominal', 15, 2)->default(0);
            $table->string('status_pembayaran')->default('belum_dibayar')->comment('belum_dibayar, sebagian, lunas');
            $table->text('keterangan')->nullable();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('vendor_id')->references('id')->on('vendor_supirs')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_tagihan_vendors');
    }
};
