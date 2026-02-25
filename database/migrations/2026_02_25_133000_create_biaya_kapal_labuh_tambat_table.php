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
        Schema::create('biaya_kapal_labuh_tambat', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->foreignId('biaya_kapal_id')->constrained('biaya_kapals')->onDelete('cascade');
            $blueprint->string('kapal')->nullable();
            $blueprint->string('voyage')->nullable();
            $blueprint->string('nomor_referensi')->nullable();
            $blueprint->string('vendor')->nullable();
            $blueprint->string('lokasi')->nullable();
            $blueprint->unsignedBigInteger('type_id')->nullable();
            $blueprint->string('type_keterangan')->nullable();
            $blueprint->boolean('is_lumpsum')->default(false);
            $blueprint->decimal('kuantitas', 15, 2)->default(0);
            $blueprint->decimal('harga', 15, 2)->default(0);
            $blueprint->decimal('sub_total', 15, 2)->default(0);
            $blueprint->decimal('pph', 15, 2)->default(0);
            $blueprint->decimal('grand_total', 15, 2)->default(0);
            $blueprint->string('penerima')->nullable();
            $blueprint->string('nomor_rekening')->nullable();
            $blueprint->date('tanggal_invoice_vendor')->nullable();
            $blueprint->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_kapal_labuh_tambat');
    }
};
