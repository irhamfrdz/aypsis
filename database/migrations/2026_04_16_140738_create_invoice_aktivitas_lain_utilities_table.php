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
        Schema::create('invoice_aktivitas_lain_utilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_aktivitas_lain_id')->constrained('invoice_aktivitas_lain')->onDelete('cascade');
            $table->foreignId('alat_berat_id')->nullable()->constrained('alat_berats')->onDelete('set null');
            $table->string('referensi')->nullable();
            $table->string('penerima')->nullable();
            $table->date('tanggal')->nullable();
            $table->enum('jenis_tarif', ['harian', 'bulanan'])->default('harian');
            $table->decimal('jumlah_periode', 10, 2)->default(1);
            $table->decimal('tarif_satuan', 15, 2)->default(0);
            $table->decimal('dpp', 15, 2)->default(0);
            $table->decimal('pph', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_aktivitas_lain_utilities');
    }
};
