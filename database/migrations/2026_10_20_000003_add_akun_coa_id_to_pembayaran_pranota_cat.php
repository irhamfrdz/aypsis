<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran_pranota_cat', function (Blueprint $table) {
            $table->foreignId('akun_coa_id')->nullable()->constrained('akun_coa')->nullOnDelete();
        });

        $akunId = DB::table('akun_coa')->where('nama_akun', 'Biaya CAT Kontainer')->value('id');
        if ($akunId) {
            DB::table('pembayaran_pranota_cat')->whereNull('akun_coa_id')->update(['akun_coa_id' => $akunId]);
        }
    }

    public function down(): void
    {
        Schema::table('pembayaran_pranota_cat', function (Blueprint $table) {
            $table->dropConstrainedForeignId('akun_coa_id');
        });
    }
};
