<?php

namespace App\Services;

use App\Models\PranotaUangRitKenek;
use Illuminate\Support\Collection;

class RekapUangRitService
{
    public function references($pranota): Collection
    {
        $references = collect(preg_split('/[,;\r\n]+/', $pranota->no_surat_jalan ?? ''))
            ->map(fn ($number) => trim($number))->filter()->values();

        if ($references->isEmpty()) {
            if ($pranota->suratJalan) {
                $references->push($pranota->suratJalan->no_surat_jalan);
            }
            if ($pranota->suratJalanBongkaran) {
                $references->push($pranota->suratJalanBongkaran->nomor_surat_jalan.' (Bongkaran)');
            }
        }

        return $references->map(fn ($number) => [
            'number' => trim(preg_replace('/\s*\(Bongkaran\)\s*$/i', '', $number)),
            'bongkaran' => (bool) preg_match('/\(Bongkaran\)\s*$/i', $number),
        ]);
    }

    /** Follow the equal split used by the pranota detail pages for combined surat jalan. */
    public function entries($pranota, Collection $muat, Collection $bongkaran, string $kapal, string $voyage, string $lokasi): Collection
    {
        if ($lokasi !== '' && $lokasi !== 'jakarta') {
            return collect();
        }
        $references = $this->references($pranota);
        $rit = $pranota instanceof PranotaUangRitKenek ? $pranota->uang_rit_kenek : $pranota->uang_rit_supir;
        // Debt, savings and BPJS deductions change payment amounts, not voyage costs.
        $amount = (float) ($pranota->total_uang ?? $rit ?? $pranota->uang_rit ?? $pranota->total_rit ?? 0)
            + (float) ($pranota->total_adjustment ?? 0);

        return $references->map(function ($reference) use ($muat, $bongkaran, $kapal, $voyage, $amount, $references) {
            $sj = ($reference['bongkaran'] ? $bongkaran : $muat)->get($reference['number']);
            if (! $sj) {
                return null;
            }
            $matches = fn ($row) => $this->ship($row->nama_kapal ?? '') === $this->ship($kapal)
                && strtolower(trim($row->no_voyage ?? '')) === strtolower(trim($voyage));
            $ratio = $reference['bongkaran'] ? ($matches($sj) ? 1 : 0)
                : ($sj->prospeks->isEmpty() ? 0 : $sj->prospeks->filter($matches)->count() / $sj->prospeks->count());

            return $ratio > 0 ? ['surat_jalan' => $sj, 'nomor' => $reference['number'], 'biaya' => $amount / $references->count() * $ratio] : null;
        })->filter()->values();
    }

    /** Keep the original pranota ID for detail links while displaying one row per surat jalan. */
    public function driverRows($pranota): Collection
    {
        return collect($pranota->rekap_rit_items)->map(function ($entry) use ($pranota) {
            $row = clone $pranota;
            $row->is_rit_supir_detail = true;
            $row->rekap_rit_items = [$entry];
            $row->setRelation('rekapSuratJalan', $entry['surat_jalan']);
            $row->supir_nama = $entry['surat_jalan']->supir ?: $pranota->supir_nama;
            $amount = round($entry['biaya'], 2);
            $row->apportioned = ['nominal' => $amount, 'ppn' => 0, 'pph' => 0, 'total_biaya' => $amount];

            return $row;
        });
    }

    private function ship(string $name): string
    {
        return preg_replace('/[^a-z0-9]/', '', preg_replace('/^km[.\s]+/i', '', strtolower(trim($name))));
    }
}
