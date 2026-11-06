<?php

use App\Services\StockBanUsageHistoryService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('stock_ban_usage_histories')) {
            Schema::create('stock_ban_usage_histories', function (Blueprint $table) {
                $table->id();
                $table->string('source_type', 30);
                $table->unsignedBigInteger('source_id');
                $table->unsignedBigInteger('audit_log_id')->nullable()->unique();
                $table->unsignedBigInteger('mobil_id')->nullable()->index();
                $table->unsignedBigInteger('alat_berat_id')->nullable();
                $table->date('tanggal_digunakan')->index();
                $table->string('unit_name')->nullable();
                $table->string('nama_ban')->nullable();
                $table->string('merk')->nullable();
                $table->string('ukuran')->nullable();
                $table->string('nomor_seri')->nullable();
                $table->string('nomor_bukti_pakai')->nullable();
                $table->unsignedBigInteger('penerima_id')->nullable();
                $table->string('penerima_name')->nullable();
                $table->text('keterangan')->nullable();
                $table->timestamps();
                $table->index(['source_type', 'source_id']);
            });
        }

        (new StockBanUsageHistoryService)->backfill();
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_ban_usage_histories');
    }
};
