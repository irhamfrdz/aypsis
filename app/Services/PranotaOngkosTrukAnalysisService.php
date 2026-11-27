<?php

namespace App\Services;

use App\Models\PranotaOngkosTruk;
use Illuminate\Support\Carbon;

class PranotaOngkosTrukAnalysisService
{
    public function analyze(PranotaOngkosTruk $pranota): array
    {
        $relations = ['tujuanPengambilanRelation', 'tandaTerima', 'supirKaryawan', 'kenekKaryawan', 'uangJalan.pranotaUangJalan.pembayaranPranotaUangJalans'];
        $pranota->load(array_merge(
            array_map(fn ($relation) => 'items.suratJalan.'.$relation, $relations),
            array_map(fn ($relation) => 'items.suratJalanBongkaran.'.$relation, $relations)
        ));
        $components = [
            'jumlah_uang_jalan' => 'Dasar', 'jumlah_mel' => 'MEL',
            'jumlah_pelancar' => 'Pelancar', 'jumlah_kawalan' => 'Kawalan',
            'jumlah_parkir' => 'Parkir', 'jumlah_penyesuaian' => 'Penyesuaian',
        ];
        $rows = $pranota->items->values()->map(function ($item, $index) use ($components) {
            $sj = $item->type === 'SuratJalan' ? $item->suratJalan : $item->suratJalanBongkaran;
            $uj = $sj?->uangJalan;
            $destination = $sj?->tujuanPengambilanRelation;
            $ongkos = $destination
                ? (str_contains(strtolower($sj->size ?? ''), '40') ? $destination->ongkos_truk_40ft : $destination->ongkos_truk_20ft)
                : 0;
            if ($sj?->tujuan_pengambilan === 'PULO GADUNG ( BESI SCRAP )') {
                $ongkos = 1050000;
            }
            $tt = $item->type === 'SuratJalan'
                ? ($sj?->tanggal_tanda_terima ?: $sj?->tandaTerima?->tanggal)
                : $sj?->tandaTerima?->tanggal_tanda_terima;
            $bukti = $uj ? $uj->pranotaUangJalan->flatMap(fn ($p) => $p->pembayaranPranotaUangJalans->pluck('nomor_accurate'))->filter()->unique()->implode(', ') : '';
            $values = [];
            foreach ($components as $field => $label) {
                $values[$field] = (float) ($uj?->{$field} ?? 0);
            }

            return [
                'no' => $index + 1,
                'tgl_sj' => ($sj?->tanggal_surat_jalan ?: $item->tanggal)?->format('d/m/Y') ?? '-',
                'tgl_tt' => $tt ? Carbon::parse($tt)->format('d/m/Y') : '-',
                'sj' => $item->no_surat_jalan, 'plat' => $sj?->no_plat ?? '-',
                'size' => $sj?->size ?? '-',
                'rit_s' => $sj && ($sj->supir || $sj->supir2 || $sj->supirKaryawan) ? 1 : 0,
                'rit_k' => $sj && ($sj->kenek || $sj->kenekKaryawan) ? 1 : 0,
                'bukti' => $bukti ?: '-', 'tujuan' => $destination?->ke ?? $sj?->tujuan_pengambilan ?? '-',
                'ongkos' => (float) $ongkos, 'uj' => (float) ($uj?->jumlah_total ?? 0),
                'nominal' => (float) $item->nominal, 'components' => $values,
                'uj_number' => $uj?->nomor_uang_jalan, 'has_uj' => $uj !== null,
                'memo' => $uj?->memo, 'reason' => $uj?->alasan_penyesuaian,
                'pengirim' => trim((string) ($sj?->pengirim ?? '')) ?: '-',
            ];
        });
        $groupKey = fn ($row) => json_encode([preg_replace('/\s+/', '', strtoupper($row['plat'])), mb_strtoupper(trim($row['tujuan']))]);
        $groups = $rows->groupBy($groupKey);
        $summaries = [];
        foreach ($groups as $group) {
            if ($group->pluck('uj')->unique()->count() <= 1) {
                continue;
            }
            $changes = [];
            foreach ($components as $field => $label) {
                $values = $group->map(fn ($row) => $row['components'][$field])->unique()->sort()->values();
                if ($values->count() > 1) {
                    $changes[] = $label.': '.$values->map(fn ($value) => number_format($value, 0, ',', '.'))->implode(' / ');
                }
            }
            $summaries[] = [
                'plat' => $group->first()['plat'], 'tujuan' => $group->first()['tujuan'],
                'numbers' => $group->pluck('no')->implode(', '),
                'totals' => $group->pluck('uj')->unique()->sort()->map(fn ($value) => number_format($value, 0, ',', '.'))->implode(' / '),
                'changes' => implode('; ', $changes) ?: 'Total berbeda; perlu periksa kesesuaian komponen.',
                'difference' => $group->max('uj') - $group->min('uj'),
            ];
        }
        $rows = $rows->map(function ($row) use ($groups, $groupKey, $components) {
            $group = $groups[$groupKey($row)];
            $parts = [];
            foreach ($components as $field => $label) {
                if ($row['components'][$field] != 0) {
                    $parts[] = $label.' '.number_format($row['components'][$field], 0, ',', '.');
                }
            }
            $note = $row['has_uj'] ? ($row['uj_number'] ?? 'UJ tanpa nomor').': '.(implode(' + ', $parts) ?: '0').' = '.number_format($row['uj'], 0, ',', '.').'.' : 'Data uang jalan belum tersedia; tampilan UJ 0.';
            if ($group->pluck('uj')->unique()->count() > 1) {
                $base = $group->sortBy('uj')->first();
                $changed = [];
                foreach ($components as $field => $label) {
                    if ($group->map(fn ($r) => $r['components'][$field])->unique()->count() > 1) {
                        $changed[] = $label;
                    }
                }
                $note .= ' Berbeda pada '.(implode(', ', $changed) ?: 'total tersimpan').'.';
                $note .= $base['no'] === $row['no'] ? ' Acuan UJ terendah kelompok.' : ' Selisih '.number_format($row['uj'] - $base['uj'], 0, ',', '.').' terhadap no. '.$base['no'].'.';
                if ($group->contains(fn ($r) => ! $r['has_uj'])) {
                    $note .= ' Kelompok mencakup SJ tanpa data UJ.';
                }
                if ($group->pluck('size')->map(fn ($size) => preg_replace('/[^0-9]/', '', $size))->unique()->count() > 1) {
                    $note .= ' Ukuran kontainer juga berbeda.';
                }
            } else {
                $note .= $group->count() > 1 ? ' UJ sama pada plat dan tujuan ini.' : ' Tidak ada pembanding plat dan tujuan yang sama.';
            }
            if ($row['has_uj'] && abs(array_sum($row['components']) - $row['uj']) > 0.01) {
                $note .= ' PERIKSA: jumlah komponen tidak sama dengan total UJ tersimpan.';
            }
            foreach ([$row['memo'], $row['reason']] as $extra) {
                if ($extra) {
                    $note .= ' '.$extra;
                }
            }
            $row['keterangan'] = 'Pengirim: '.$row['pengirim'].'. '.$note;

            return $row;
        });

        return compact('rows', 'summaries');
    }
}
