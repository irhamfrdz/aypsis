<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            if (!Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'tarif_nominal')) {
                $table->decimal('tarif_nominal', 15, 2)->nullable()->after('tarif');
            }
        });

        // Backfill: prefer existing dpp when present; otherwise try to look up pricelist
        $rows = DB::table('daftar_tagihan_kontainer_sewa')->get();
        foreach ($rows as $r) {
            $value = null;
            if (!is_null($r->dpp) && $r->dpp > 0) {
                $value = $r->dpp;
            } else {
                // Try to find kontainer to get ukuran, then pricelist by vendor+ukuran+date
                if (!empty($r->nomor_kontainer) && !empty($r->vendor)) {
                    $kont = DB::table('kontainers')->where('nomor_seri_gabungan', $r->nomor_kontainer)->first();
                    if ($kont) {
                        $date = $r->tanggal_awal ?? date('Y-m-d');
                        $pr = DB::table('master_pricelist_sewa_kontainers')
                            ->where('vendor', $r->vendor)
                            ->where('ukuran_kontainer', $kont->ukuran)
                            ->where(function($q) use ($date) {
                                $q->whereNull('tanggal_harga_awal')->orWhere('tanggal_harga_awal', '<=', $date);
                            })
                            ->where(function($q) use ($date) {
                                $q->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir', '>=', $date);
                            })
                            ->orderByDesc('tanggal_harga_awal')
                            ->first();
                        if ($pr) {
                            $value = $pr->tarif ?? $pr->harga ?? null;
                        }
                    }
                }
            }

            if (!is_null($value)) {
                DB::table('daftar_tagihan_kontainer_sewa')->where('id', $r->id)->update(['tarif_nominal' => $value]);
            }
        }
    }

    public function down()
    {
        Schema::table('daftar_tagihan_kontainer_sewa', function (Blueprint $table) {
            if (Schema::hasColumn('daftar_tagihan_kontainer_sewa', 'tarif_nominal')) {
                $table->dropColumn('tarif_nominal');
            }
        });
    }
};
