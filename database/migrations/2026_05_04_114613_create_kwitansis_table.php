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
        Schema::create('kwitansis', function (Blueprint $table) {
            $table->id();
            $table->string('pelanggan_kode')->nullable();
            $table->string('pelanggan_nama')->nullable();
            $table->text('terima_dari')->nullable();
            $table->text('kirim_ke')->nullable();
            $table->string('no_po')->nullable();
            $table->string('kwt_no')->unique();
            $table->date('tgl_inv')->nullable();
            $table->date('tgl_kirim')->nullable();
            $table->string('fob')->nullable();
            $table->string('syarat_pembayaran')->nullable();
            $table->string('pengirim')->nullable();
            $table->string('penjual')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('akun_piutang')->nullable();
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('discount_persen', 5, 2)->default(0);
            $table->decimal('discount_nominal', 15, 2)->default(0);
            $table->decimal('biaya_kirim', 15, 2)->default(0);
            $table->decimal('total_invoice', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kwitansis');
    }
};
