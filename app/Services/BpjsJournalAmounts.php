<?php

namespace App\Services;

use App\Models\PranotaBpjsDetail;

class BpjsJournalAmounts
{
    public function forDetail(PranotaBpjsDetail $detail, ?string $component = null): array
    {
        $employee = $detail->karyawan_pranota;
        $parts = $detail->rincian_bpjs;
        $cents = fn ($value) => (int) round((float) $value * 100);
        $sum = fn ($fields) => array_sum(array_map(fn ($field) => $cents($parts !== null ? ($parts[$field] ?? 0) : ($detail->$field ?? 0)), $fields));
        $compact = fn ($value) => preg_replace('/[\s_-]+/u', '', mb_strtoupper((string) $value));
        $jkn = $component === 'jamsostek' ? 0 : $cents($detail->bpjs_kesehatan);
        $debtJkn = $component === 'jamsostek' ? 0 : $cents($detail->bpjs_ketenagakerjaan);
        $biayaJkn = $component === 'jamsostek' ? 0 : $cents($detail->kis_biaya);
        if ($compact($employee?->group_jkn) === 'JKNKIS' && $compact($employee?->tipe_jkn) === 'NONKARY') {
            $tunjangan = 0;
            $biaya = $jkn + $debtJkn;
            $hutang = 0;
        } else {
            $tunjangan = $jkn - $biayaJkn;
            $biaya = $biayaJkn;
            $hutang = $debtJkn;
        }
        $cost = $component === 'jkn' ? 0 : $sum(['jht_biaya', 'noncrew_jht_biaya', 'jp_biaya']);
        $debt = $component === 'jkn' ? 0 : $sum(['jht_hutang', 'jkk_hutang', 'noncrew_jht_hutang', 'jp_hutang']);
        $allowance = $component === 'jkn' ? 0 : $sum(['jkk_tunjangan', 'jkm_tunjangan', 'bpu_jkk_tunjangan', 'bpu_jkm', 'noncrew_jkk_tunjangan', 'noncrew_jkm_tunjangan']);
        $type = $compact($employee?->tipe_bp_jamsostek);
        $group = $compact($employee?->group_bp_jamsostek);
        if ($type === 'NONKARY' && $group === 'PPU') {
            $biaya += $cost + $debt + $allowance;
        } else {
            $shift = $component !== 'jkn' && $type === 'NONKARYUMKMNONPP' && in_array($group, ['BPUSUPIR', 'BPUSUPIRJKTPLUIT', 'CILANDAKHL'], true)
                ? $sum($parts === null ? ['jkk_tunjangan', 'jkm_tunjangan'] : ['noncrew_jkk_tunjangan', 'noncrew_jkm_tunjangan']) : 0;
            $biaya += $cost + $shift;
            $tunjangan += $allowance - $shift;
            $hutang += $debt;
        }

        return [$tunjangan / 100, $biaya / 100, $hutang / 100];
    }
}
