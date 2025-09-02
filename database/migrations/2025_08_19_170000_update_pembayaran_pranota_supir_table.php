<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pembayaran_pranota_supir', function (Blueprint $table) {
            $table->date('tanggal_kas')->nullable()->after('id');
            $table->string('bank')->nullable()->after('total_pembayaran');
            $table->string('jenis_transaksi')->nullable()->after('bank');
            $table->decimal('total_tagihan_penyesuaian', 15, 2)->nullable()->after('jenis_transaksi');
            $table->decimal('total_tagihan_setelah_penyesuaian', 15, 2)->nullable()->after('total_tagihan_penyesuaian');
            $table->text('alasan_penyesuaian')->nullable()->after('total_tagihan_setelah_penyesuaian');
            $table->text('keterangan')->change();
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('pembayaran_pranota_supir', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_kas',
                'bank',
                'jenis_transaksi',
                'total_tagihan_penyesuaian',
                'total_tagihan_setelah_penyesuaian',
                'alasan_penyesuaian',
            ]);
            $table->string('keterangan')->change();
        });
    }
};
