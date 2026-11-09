<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pranota_uang_rits', function (Blueprint $table) {
            $table->date('periode_dari')->nullable()->after('tanggal');
            $table->date('periode_sampai')->nullable()->after('periode_dari');
        });

        // Periode pranota ini dikonfirmasi oleh pengguna; pranota lama lain tidak
        // mempunyai periode tersimpan sehingga tidak dapat diisi dengan pasti.
        DB::table('pranota_uang_rits')
            ->where('no_pranota', 'PUR-11-26-000048')
            ->update([
                'periode_dari' => '2026-10-31',
                'periode_sampai' => '2026-11-06',
            ]);
    }

    public function down(): void
    {
        Schema::table('pranota_uang_rits', function (Blueprint $table) {
            $table->dropColumn(['periode_dari', 'periode_sampai']);
        });
    }
};
