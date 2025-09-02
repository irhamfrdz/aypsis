<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('permohonans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_memo')->unique();
            $table->string('kegiatan');
            $table->string('vendor_perusahaan');
            $table->foreignId('supir_id')->nullable()->constrained('karyawans')->onDelete('set null');
            $table->foreignId('krani_id')->nullable()->constrained('karyawans')->onDelete('set null');
            $table->string('plat_nomor')->nullable();
            $table->string('no_chasis')->nullable();
            $table->string('ukuran');
            $table->string('tujuan');
            $table->integer('jumlah_kontainer')->default(1);
            $table->date('tanggal_memo');
            $table->decimal('jumlah_uang_jalan', 15, 2)->default(0);
            $table->decimal('adjustment', 15, 2)->default(0);
            $table->string('alasan_adjustment')->nullable();
            $table->decimal('total_harga_setelah_adj', 15, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->string('lampiran')->nullable();
            $table->string('status')->default('Baru');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('permohonans');
    }
};
