<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tagihan_service_kendaraan_batams', function (Blueprint $table) {
            $table->string('nomor_polisi', 50)->change();
            $table->unsignedBigInteger('mobil_id')->nullable()->after('tanggal_service');
            $table->index('mobil_id', 'svc_batam_mobil_idx');
            $table->foreign('mobil_id', 'svc_batam_mobil_fk')
                ->references('id')->on('mobils')->nullOnDelete();
        });

        foreach (DB::table('tagihan_service_kendaraan_batams')->whereNotNull('nomor_polisi')->get() as $tagihan) {
            $mobilId = DB::table('mobils')
                ->where('nomor_polisi', $tagihan->nomor_polisi)
                ->orWhere('no_kir', $tagihan->nomor_polisi)
                ->value('id');

            if ($mobilId) {
                DB::table('tagihan_service_kendaraan_batams')
                    ->where('id', $tagihan->id)
                    ->update(['mobil_id' => $mobilId]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('tagihan_service_kendaraan_batams', function (Blueprint $table) {
            $table->dropForeign('svc_batam_mobil_fk');
            $table->dropIndex('svc_batam_mobil_idx');
            $table->dropColumn('mobil_id');
        });
    }
};
