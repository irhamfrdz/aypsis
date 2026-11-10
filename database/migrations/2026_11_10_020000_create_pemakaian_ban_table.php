<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemakaian_ban', function (Blueprint $table) {
            $table->id();

            // Identifies the original usage row, not the reusable stock item.
            $table->string('source_table', 64)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unique(['source_table', 'source_id']);

            // ban, ban_dalam, ban_perut, ring_velg, velg.
            $table->string('jenis_komponen', 30)->index();
            $table->string('stock_table', 64);
            $table->unsignedBigInteger('stock_id');
            $table->index(['stock_table', 'stock_id']);
            $table->unsignedBigInteger('audit_log_id')->nullable()->index();

            $table->date('tanggal_digunakan')->index();
            $table->date('tanggal_keluar')->nullable();
            $table->unsignedInteger('qty')->default(1);

            // Keep historical snapshots even if a master record changes or is deleted.
            $table->unsignedBigInteger('mobil_id')->nullable();
            $table->unsignedBigInteger('alat_berat_id')->nullable()->index();
            $table->unsignedBigInteger('kapal_id')->nullable();
            $table->unsignedBigInteger('gudang_id')->nullable();
            $table->string('unit_name')->nullable();
            $table->index(['mobil_id', 'tanggal_digunakan']);

            $table->string('nama_barang');
            $table->string('merk')->nullable();
            $table->string('ukuran')->nullable();
            $table->string('nomor_seri')->nullable();
            $table->string('nomor_bukti_pakai')->nullable()->index();
            $table->unsignedBigInteger('penerima_id')->nullable();
            $table->string('penerima_name')->nullable();
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemakaian_ban');
    }
};
