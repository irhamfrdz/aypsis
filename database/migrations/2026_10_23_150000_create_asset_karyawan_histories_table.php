<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_karyawan_histories', function (Blueprint $table) {
            $table->id();
            $table->string('source_table', 150);
            $table->string('source_id', 100);
            $table->unsignedBigInteger('karyawan_id')->nullable()->index();
            $table->string('nama_karyawan')->nullable();
            $table->string('nik')->nullable();
            $table->string('kode_asset')->nullable();
            $table->string('nama_asset')->nullable();
            $table->string('kategori_asset')->nullable();
            $table->date('tanggal_tanda_terima_asset')->nullable();
            $table->string('kondisi_fisik')->nullable();
            $table->string('status_operasional')->nullable();
            $table->boolean('is_active');
            $table->string('event', 50);
            $table->json('snapshot');
            $table->string('fingerprint', 64);
            $table->unsignedBigInteger('recorded_by')->nullable();
            $table->timestamp('recorded_at')->useCurrent()->index();
            $table->index(['source_table', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_karyawan_histories');
    }
};
