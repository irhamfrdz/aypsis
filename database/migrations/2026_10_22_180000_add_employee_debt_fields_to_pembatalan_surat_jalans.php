<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembatalan_surat_jalans', function (Blueprint $table) {
            $table->string('metode_pembatalan', 30)->default('kas')->after('tanggal_pembayaran');
            $table->foreignId('karyawan_id')->nullable()->after('metode_pembatalan')->constrained('karyawans')->nullOnDelete();
            $table->foreignId('akun_piutang_id')->nullable()->after('karyawan_id')->constrained('akun_coa')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pembatalan_surat_jalans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('akun_piutang_id');
            $table->dropConstrainedForeignId('karyawan_id');
            $table->dropColumn('metode_pembatalan');
        });
    }
};
