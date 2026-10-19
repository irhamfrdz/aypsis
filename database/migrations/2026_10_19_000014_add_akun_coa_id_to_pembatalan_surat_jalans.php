<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembatalan_surat_jalans', function (Blueprint $table) {
            $table->foreignId('akun_coa_id')->nullable()->constrained('akun_coa')->nullOnDelete();
        });

        $akunLamaId = DB::table('akun_coa')->where('nama_akun', 'Biaya Uang Jalan Muat')->value('id');
        if ($akunLamaId) {
            DB::table('pembatalan_surat_jalans')->whereNull('akun_coa_id')->update(['akun_coa_id' => $akunLamaId]);
        }
    }

    public function down(): void
    {
        Schema::table('pembatalan_surat_jalans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('akun_coa_id');
        });
    }
};
