<?php

namespace App\Services;

use App\Models\PranotaUangRitKenek;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;

class PranotaUangRitKenekStatus
{
    public function sync(PranotaUangRitKenek $pranota, string $status): void
    {
        $references = (new RekapUangRitService)->references($pranota);
        $regularNumbers = $references->where('bongkaran', false)->pluck('number');
        $bongkaranNumbers = $references->where('bongkaran', true)->pluck('number');

        $regular = SuratJalan::where(function ($query) use ($pranota, $regularNumbers) {
            $query->whereIn('no_surat_jalan', $regularNumbers);
            if ($pranota->surat_jalan_id !== null) {
                $query->orWhere('id', $pranota->surat_jalan_id);
            }
        })->lockForUpdate()->get();

        foreach ($regular as $suratJalan) {
            $suratJalan->update(['status_pembayaran_uang_rit_kenek' => $status]);
        }

        // The bongkaran column supports only belum_bayar/lunas.
        $bongkaranStatus = $status === SuratJalan::STATUS_UANG_RIT_BELUM_DIBAYAR ? 'belum_bayar' : 'lunas';
        $bongkaran = SuratJalanBongkaran::where(function ($query) use ($pranota, $bongkaranNumbers) {
            $query->whereIn('nomor_surat_jalan', $bongkaranNumbers);
            if ($pranota->surat_jalan_bongkaran_id !== null) {
                $query->orWhere('id', $pranota->surat_jalan_bongkaran_id);
            }
        })->lockForUpdate()->get();

        foreach ($bongkaran as $suratJalan) {
            $suratJalan->update(['status_pembayaran_uang_rit_kenek' => $bongkaranStatus]);
        }
    }
}
