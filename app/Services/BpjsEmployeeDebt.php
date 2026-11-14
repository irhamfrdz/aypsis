<?php

namespace App\Services;

class BpjsEmployeeDebt
{
    public static function apply(array $detail): array
    {
        if (! isset($detail['allowance_kary_jht'])) {
            return $detail;
        }
        $type = preg_replace('/[\s_-]+/', '', strtoupper($detail['snapshot_karyawan']['tipe_bp_jamsostek'] ?? ''));
        if (in_array($type, ['NONKARY', 'NONKARYUMKMNONPP'], true)) {
            return $detail;
        }
        $fields = ['jht_hutang', 'jkk_hutang', 'noncrew_jht_hutang', 'jp_hutang'];
        $parse = fn ($value) => is_numeric($value) ? (float) $value : (float) str_replace(',', '.', str_replace('.', '', (string) $value));
        $amounts = array_map(fn ($key) => max(0, (int) round($parse($detail[$key] ?? 0) * 100)), $fields);
        $target = max(0, (int) round((float) $detail['allowance_kary_jht'] * 100));
        $sum = array_sum($amounts);
        $remaining = $target;
        $active = array_keys(array_filter($amounts));
        if (! $active) {
            $group = preg_replace('/[\s_-]+/', '', strtoupper($detail['snapshot_karyawan']['group_bp_jamsostek'] ?? ''));
            $active = [str_contains($group, 'PPU') ? 0 : ($group === 'BPUCREW' ? 1 : 2)];
        }
        $last = end($active);
        foreach ($fields as $index => $key) {
            $cents = ! in_array($index, $active, true) ? 0 : ($index === $last ? $remaining : min($remaining, (int) round($target * $amounts[$index] / $sum)));
            $detail[$key] = $cents / 100;
            $remaining -= $cents;
        }
        return $detail;
    }
}
