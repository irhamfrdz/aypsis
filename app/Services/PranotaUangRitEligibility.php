<?php

namespace App\Services;

use App\Models\PranotaUangRit;
use App\Models\SuratJalan;
use Illuminate\Database\Eloquent\Builder;

class PranotaUangRitEligibility
{
    public static function usedNumbers(): array
    {
        $used = ['regular' => [], 'bongkaran' => []];

        $pranotas = PranotaUangRit::query()
            ->whereNotIn('status', ['cancelled'])
            ->whereNotNull('no_surat_jalan')
            ->select('no_surat_jalan')
            ->cursor();

        foreach ($pranotas as $pranota) {
            foreach (preg_split('/\s*,\s*/', $pranota->no_surat_jalan) as $number) {
                $number = trim($number);
                if ($number === '') {
                    continue;
                }

                $isBongkaran = str_ends_with($number, ' (Bongkaran)');
                if ($isBongkaran) {
                    $number = substr($number, 0, -strlen(' (Bongkaran)'));
                }
                $used[$isBongkaran ? 'bongkaran' : 'regular'][$number] = true;
            }
        }

        return [
            'regular' => array_keys($used['regular']),
            'bongkaran' => array_keys($used['bongkaran']),
        ];
    }

    public static function regular(Builder $query): Builder
    {
        return $query
            ->where('status_pembayaran_uang_rit', SuratJalan::STATUS_UANG_RIT_BELUM_DIBAYAR)
            ->whereNotIn('id', function ($subQuery) {
                $subQuery->select('surat_jalan_id')
                    ->from('pranota_uang_rits')
                    ->whereNotNull('surat_jalan_id')
                    ->whereNotIn('status', ['cancelled']);
            });
    }

    public static function bongkaran(Builder $query): Builder
    {
        return $query
            ->where(function ($q) {
                $q->where('status_pembayaran_uang_rit', 'belum_bayar')
                    ->orWhereNull('status_pembayaran_uang_rit');
            })
            ->whereNotIn('id', function ($subQuery) {
                $subQuery->select('surat_jalan_bongkaran_id')
                    ->from('pranota_uang_rits')
                    ->whereNotNull('surat_jalan_bongkaran_id')
                    ->whereNotIn('status', ['cancelled']);
            });
    }
}
