<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran_aktivitas_lains', function (Blueprint $table) {
            $table->unsignedBigInteger('akun_pph_id')->nullable()->after('akun_bank_id');
        });

        $defaultAccountId = DB::table('akun_coa')->where('nomor_akun', '2102.003')->value('id');
        if ($defaultAccountId) {
            DB::table('pembayaran_aktivitas_lains')
                ->where('pph_nominal', '>', 0)
                ->update(['akun_pph_id' => $defaultAccountId]);
        }
    }

    public function down(): void
    {
        Schema::table('pembayaran_aktivitas_lains', function (Blueprint $table) {
            $table->dropColumn('akun_pph_id');
        });
    }
};
