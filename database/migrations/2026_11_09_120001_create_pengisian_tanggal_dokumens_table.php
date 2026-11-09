<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengisian_tanggal_dokumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kapal_id')->constrained('master_kapals')->cascadeOnDelete();
            $table->string('nama_kapal');
            $table->string('no_voyage');
            $table->string('jenis_dokumen', 32);
            $table->string('nomor_dokumen');
            $table->string('identifier_hash', 64)->unique();
            $table->date('tanggal_terima_dokumen_jkt_customer')->nullable();
            $table->date('tanggal_terima_dokumen_packinglist')->nullable();
            $table->date('tanggal_dokumen_invoice_faktur_pajak')->nullable();
            $table->date('tanggal_kirim_dokumen_ppjk')->nullable();
            $table->date('tanggal_terbit_sppb_ppftz')->nullable();
            $table->date('tanggal_terima_dokumen_dari_ppjk')->nullable();
            $table->date('tanggal_kirim_dokumen_jakarta')->nullable();
            $table->date('tanggal_terima_sj_cabang')->nullable();
            $table->date('tanggal_terima_ppftz_cabang')->nullable();
            $table->timestamps();
            $table->index(['kapal_id', 'no_voyage']);
        });

        DB::table('permissions')->insertOrIgnore([
            'name' => 'pengisian-tanggal-dokumen-manage',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pengisian_tanggal_dokumens');
        DB::table('permissions')->where('name', 'pengisian-tanggal-dokumen-manage')->delete();
    }
};
