<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pembayaran_pranota_uang_jalans', 'memo_biaya')) {
            Schema::table('pembayaran_pranota_uang_jalans', function (Blueprint $table) {
                $table->text('memo_biaya')->nullable();
            });
        }

        if (! Schema::hasTable('pembayaran_pranota_uang_jalan_akun_items')) {
            Schema::create('pembayaran_pranota_uang_jalan_akun_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pembayaran_pranota_uang_jalan_id')
                    ->constrained('pembayaran_pranota_uang_jalans', 'id', 'fk_ppuj_akun_payment')->cascadeOnDelete();
                $table->foreignId('akun_coa_id')
                    ->constrained('akun_coa', 'id', 'fk_ppuj_akun_coa')->restrictOnDelete();
                $table->decimal('nominal', 15, 2);
                $table->text('memo')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_pranota_uang_jalan_akun_items');
        if (Schema::hasColumn('pembayaran_pranota_uang_jalans', 'memo_biaya')) {
            Schema::table('pembayaran_pranota_uang_jalans', fn (Blueprint $table) => $table->dropColumn('memo_biaya'));
        }
    }
};
