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
        Schema::create('biaya_kapal_meratus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('biaya_kapal_id')->constrained('biaya_kapals')->onDelete('cascade');
            $table->string('kapal')->nullable();
            $table->string('voyage')->nullable();
            $table->string('nomor_referensi')->nullable();
            $table->string('pricelist_meratus_id')->nullable(); // Can be ID or MANUAL
            $table->string('jenis_biaya'); 
            $table->string('lokasi')->nullable();
            $table->string('size')->nullable();
            $table->decimal('kuantitas', 15, 2)->default(0);
            $table->decimal('harga', 15, 2)->default(0);
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('pph', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->string('penerima')->nullable();
            $table->string('nomor_rekening')->nullable();
            $table->date('tanggal_invoice_vendor')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_kapal_meratus');
    }
};
