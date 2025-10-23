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
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->enum('status_pembayaran', ['belum_bayar', 'sebagian', 'lunas'])
                  ->default('belum_bayar')
                  ->after('status')
                  ->comment('Status pembayaran: belum_bayar, sebagian, lunas');

            $table->decimal('total_tarif', 15, 2)->nullable()->after('status_pembayaran');
            $table->decimal('jumlah_terbayar', 15, 2)->default(0)->after('total_tarif');

            $table->index(['status_pembayaran']);
            $table->index(['status_pembayaran', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->dropIndex(['status_pembayaran']);
            $table->dropIndex(['status_pembayaran', 'created_at']);
            $table->dropColumn(['status_pembayaran', 'total_tarif', 'jumlah_terbayar']);
        });
    }
};
