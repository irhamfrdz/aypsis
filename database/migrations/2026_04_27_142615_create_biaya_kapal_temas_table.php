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
        Schema::create('biaya_kapal_temas', function (Blueprint $col) {
            $col->id();
            $col->foreignId('biaya_kapal_id')->constrained('biaya_kapals')->onDelete('cascade');
            $col->string('kapal')->nullable();
            $col->string('voyage')->nullable();
            $col->string('nomor_referensi')->nullable();
            $col->unsignedBigInteger('pricelist_temas_id')->nullable();
            $col->string('jenis_biaya')->nullable();
            $col->string('lokasi')->nullable();
            $col->string('size')->nullable();
            $col->decimal('kuantitas', 15, 2)->default(0);
            $col->decimal('harga', 15, 2)->default(0);
            $col->decimal('sub_total', 15, 2)->default(0);
            $col->decimal('pph', 15, 2)->default(0);
            $col->decimal('ppn', 15, 2)->default(0);
            $col->boolean('pph_active')->default(true);
            $col->boolean('ppn_active')->default(false);
            $col->decimal('biaya_materai', 15, 2)->default(0);
            $col->decimal('adjustment', 15, 2)->default(0);
            $col->decimal('grand_total', 15, 2)->default(0);
            $col->string('penerima')->nullable();
            $col->string('nomor_rekening')->nullable();
            $col->date('tanggal_invoice_vendor')->nullable();
            $col->text('keterangan')->nullable();
            $col->boolean('is_muat')->default(false);
            $col->boolean('is_bongkar')->default(false);
            $col->timestamps();
        });
    }
 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biaya_kapal_temas');
    }
};
