<?php

namespace App\Services;

use App\Models\SuratJalan;
use Illuminate\Database\Eloquent\Builder;

class PranotaUangRitEligibility
{
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
