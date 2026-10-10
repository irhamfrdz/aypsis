<?php

namespace App\Services;

use App\Models\PranotaUangRit;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

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

    public function markAvailability(Collection $regular, Collection $bongkaran): void
    {
        // A pranota stores only the first ID; the remaining surat jalan are in its number list.
        $pranotas = PranotaUangRit::where('status', '!=', 'cancelled')
            ->get(['surat_jalan_id', 'surat_jalan_bongkaran_id', 'no_surat_jalan']);
        $regularIds = $pranotas->pluck('surat_jalan_id')->filter()->flip();
        $bongkaranIds = $pranotas->pluck('surat_jalan_bongkaran_id')->filter()->flip();
        $regularNumbers = [];
        $bongkaranNumbers = [];
        foreach ($pranotas as $pranota) {
            foreach (explode(',', $pranota->no_surat_jalan ?? '') as $number) {
                $number = trim($number);
                if (str_ends_with($number, ' (Bongkaran)')) {
                    $bongkaranNumbers[substr($number, 0, -12)] = true;
                } elseif ($number !== '') {
                    $regularNumbers[$number] = true;
                }
            }
        }

        foreach ([$regular, $bongkaran] as $index => $items) {
            foreach ($items as $sj) {
                $used = $index === 0
                    ? ($regularIds->has($sj->id) || isset($regularNumbers[$sj->no_surat_jalan]))
                    : ($bongkaranIds->has($sj->id) || isset($bongkaranNumbers[$sj->nomor_surat_jalan]));
                $unpaid = in_array($sj->status_pembayaran_uang_rit, [null, 'belum_bayar', SuratJalan::STATUS_UANG_RIT_BELUM_DIBAYAR], true);
                $sj->rit_unavailable_reason = $used ? 'Sudah masuk pranota'
                    : ($unpaid ? null : 'Status pembayaran: '.$sj->status_pembayaran_uang_rit);
            }
        }
    }
}
