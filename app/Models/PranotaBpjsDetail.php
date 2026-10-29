<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PranotaBpjsDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'tunjangan_manual' => 'array',
        'rincian_bpjs' => 'array',
        'bpjs_kesehatan' => 'decimal:2',
        'bpjs_ketenagakerjaan' => 'decimal:2',
        'jht_biaya' => 'decimal:2',
        'jht_hutang' => 'decimal:2',
        'jkk_tunjangan' => 'decimal:2',
        'jkm_tunjangan' => 'decimal:2',
        'jp_biaya' => 'decimal:2',
        'jp_hutang' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function header()
    {
        return $this->belongsTo(PranotaBpjsHeader::class, 'pranota_bpjs_header_id');
    }

    public function getKisBiayaAttribute(): float
    {
        return in_array($this->tipe_karyawan, [Karyawan::class, KaryawanTidakTetap::class], true) && $this->karyawan?->is_special_case
            ? (float) $this->bpjs_kesehatan : 0;
    }

    public function getKisTunjanganAttribute(): float
    {
        return (float) $this->bpjs_kesehatan - $this->kis_biaya;
    }

    public function getTunjanganPpuAttribute(): float
    {
        if (isset($this->tunjangan_manual['ppu'])) {
            return (float) $this->tunjangan_manual['ppu'];
        }
        if (! str_contains(strtoupper((string) $this->karyawan?->group_bp_jamsostek), 'PPU')) {
            return 0;
        }

        return (float) ($this->rincian_bpjs['jkk_tunjangan'] ?? $this->jkk_tunjangan)
            + (float) ($this->rincian_bpjs['jkm_tunjangan'] ?? $this->jkm_tunjangan);
    }

    public function getTunjanganBpuAttribute(): float
    {
        $type = preg_replace('/[\s_-]+/', '', strtoupper((string) $this->karyawan?->tipe_bp_jamsostek));
        if ($type === 'NONKARYUMKMNONPP') {
            return 0;
        }
        if (isset($this->tunjangan_manual['bpu'])) {
            return (float) $this->tunjangan_manual['bpu'];
        }

        return array_sum(array_map(fn ($field) => (float) ($this->rincian_bpjs[$field] ?? 0), [
            'bpu_jkk_tunjangan', 'bpu_jkm', 'noncrew_jkk_tunjangan', 'noncrew_jkm_tunjangan',
        ]));
    }

    public function karyawan()
    {
        return $this->morphTo('karyawan', 'tipe_karyawan', 'karyawan_id');
    }

    public function summaryBurden(string $key): float
    {
        if (isset($this->tunjangan_manual[$key])) {
            return (float) $this->tunjangan_manual[$key];
        }
        $values = $this->rincian_bpjs ?? [];
        $sum = fn (array $fields) => array_sum(array_map(fn ($field) => (float) ($values[$field] ?? $this->getAttribute($field) ?? 0), $fields));
        $debt = $sum(['jht_hutang', 'jkk_hutang', 'noncrew_jht_hutang', 'jp_hutang']);
        $cost = $sum(['jht_biaya', 'noncrew_jht_biaya', 'jp_biaya']);
        $nonEmployee = fn ($type) => preg_replace('/[\s_-]+/', '', strtoupper((string) $type)) === 'NONKARY';
        $nonJkn = $nonEmployee($this->karyawan?->tipe_jkn);
        $nonJamsostek = $nonEmployee($this->karyawan?->tipe_bp_jamsostek);
        $isUmkmNonPp = preg_replace('/[\s_-]+/', '', strtoupper((string) $this->karyawan?->tipe_bp_jamsostek)) === 'NONKARYUMKMNONPP';
        $umkmCost = $this->rincian_bpjs !== null
            ? $sum(['noncrew_jht_biaya', 'noncrew_jkk_tunjangan', 'noncrew_jkm_tunjangan'])
            : $sum(['jht_biaya', 'jkk_tunjangan', 'jkm_tunjangan']);
        $compact = fn ($value) => preg_replace('/[\s_-]+/', '', strtoupper((string) $value));
        $useTotalJkn = $compact($this->karyawan?->group_jkn) === 'JKNKIS' && $compact($this->karyawan?->tipe_jkn) === 'NONKARY';
        $useTotalPpu = $compact($this->karyawan?->group_bp_jamsostek) === 'PPU' && $compact($this->karyawan?->tipe_bp_jamsostek) === 'NONKARY';
        $nonKaryTotal = ($useTotalJkn ? (float) $this->bpjs_kesehatan + (float) $this->bpjs_ketenagakerjaan : 0)
            + ($useTotalPpu ? $sum(['jht_biaya', 'jht_hutang', 'jkk_tunjangan', 'jkm_tunjangan', 'jp_biaya', 'jp_hutang']) : 0);

        return match ($key) {
            'kary_jht' => $nonJamsostek ? 0 : $debt,
            'kary_jkn' => $nonJkn ? 0 : (float) $this->bpjs_ketenagakerjaan,
            'pt' => $useTotalJkn || $useTotalPpu ? $nonKaryTotal : ($isUmkmNonPp ? $umkmCost : $cost),
            'non_kary' => ($nonJkn ? $this->kis_tunjangan + $this->kis_biaya + (float) $this->bpjs_ketenagakerjaan : 0)
                + ($nonJamsostek ? $debt + $cost
                + $sum(['jkk_tunjangan', 'jkm_tunjangan', 'bpu_jkk_tunjangan', 'bpu_jkm', 'noncrew_jkk_tunjangan', 'noncrew_jkm_tunjangan']) : 0),
            default => 0,
        };
    }
}
