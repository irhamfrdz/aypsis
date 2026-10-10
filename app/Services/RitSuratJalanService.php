<?php

namespace App\Services;

use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;

class RitSuratJalanService
{
    public function regular(CarbonInterface $start, CarbonInterface $end): Builder
    {
        return SuratJalan::where('rit', 'menggunakan_rit')
            ->where(function ($q) use ($start, $end) {
                $q->whereHas('tandaTerima', fn ($tt) => $tt
                    ->whereDate('tanggal', '>=', $start->toDateString())
                    ->whereDate('tanggal', '<=', $end->toDateString()))
                    ->orWhere(fn ($sj) => $sj->where('kegiatan', 'bongkaran')
                        ->whereDate('tanggal_tanda_terima', '>=', $start->toDateString())
                        ->whereDate('tanggal_tanda_terima', '<=', $end->toDateString()))
                    ->orWhere(fn ($sj) => $sj
                        ->whereDate('tanggal_checkpoint', '>=', $start->toDateString())
                        ->whereDate('tanggal_checkpoint', '<=', $end->toDateString()))
                    ->orWhere(fn ($sj) => $sj->where('status', 'approved')
                        ->whereDate('tanggal_surat_jalan', '>=', $start->toDateString())
                        ->whereDate('tanggal_surat_jalan', '<=', $end->toDateString()));
            });
    }

    public function bongkaran(CarbonInterface $start, CarbonInterface $end): Builder
    {
        return SuratJalanBongkaran::where(fn ($q) => $q->where('rit', 'menggunakan_rit')->orWhereNull('rit'))
            ->where(function ($q) use ($start, $end) {
                $q->whereHas('tandaTerima', fn ($tt) => $tt
                    ->whereDate('tanggal_tanda_terima', '>=', $start->toDateString())
                    ->whereDate('tanggal_tanda_terima', '<=', $end->toDateString()))
                    ->orWhere(fn ($sj) => $sj
                        ->whereDate('tanggal_checkpoint', '>=', $start->toDateString())
                        ->whereDate('tanggal_checkpoint', '<=', $end->toDateString()));
            });
    }
}
