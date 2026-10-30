<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PranotaBpjsDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'snapshot_bpjs' => 'array',
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
        if (isset($this->snapshot_bpjs['kis_biaya'])) {
            return (float) $this->snapshot_bpjs['kis_biaya'];
        }

        return in_array($this->tipe_karyawan, [Karyawan::class, KaryawanTidakTetap::class], true) && $this->karyawan_pranota?->is_special_case
            ? (float) $this->bpjs_kesehatan : 0;
    }

    public function getKisTunjanganAttribute(): float
    {
        if (isset($this->snapshot_bpjs['kis_tunjangan'])) {
            return (float) $this->snapshot_bpjs['kis_tunjangan'];
        }

        return (float) $this->bpjs_kesehatan - $this->kis_biaya;
    }

    public function getTunjanganJknAttribute(): float
    {
        if (isset($this->snapshot_bpjs['ringkasan']['jkn'])) {
            return (float) $this->snapshot_bpjs['ringkasan']['jkn'];
        }
        $type = preg_replace('/[\s_-]+/', '', strtoupper((string) $this->karyawan_pranota?->tipe_jkn));
        if ($type === 'NONKARY') {
            return 0;
        }
        if (isset($this->tunjangan_manual['jkn'])) {
            return (float) $this->tunjangan_manual['jkn'];
        }

        return $this->kis_tunjangan;
    }

    public function getTunjanganPpuAttribute(): float
    {
        if (isset($this->snapshot_bpjs['ringkasan']['ppu'])) {
            return (float) $this->snapshot_bpjs['ringkasan']['ppu'];
        }
        $type = preg_replace('/[\s_-]+/', '', strtoupper((string) $this->karyawan_pranota?->tipe_bp_jamsostek));
        if ($type === 'NONKARY') {
            return 0;
        }
        if (isset($this->tunjangan_manual['ppu'])) {
            return (float) $this->tunjangan_manual['ppu'];
        }
        if (! str_contains(strtoupper((string) $this->karyawan_pranota?->group_bp_jamsostek), 'PPU')) {
            return 0;
        }

        return (float) ($this->rincian_bpjs['jkk_tunjangan'] ?? $this->jkk_tunjangan)
            + (float) ($this->rincian_bpjs['jkm_tunjangan'] ?? $this->jkm_tunjangan);
    }

    public function getTunjanganBpuAttribute(): float
    {
        if (isset($this->snapshot_bpjs['ringkasan']['bpu'])) {
            return (float) $this->snapshot_bpjs['ringkasan']['bpu'];
        }
        $type = preg_replace('/[\s_-]+/', '', strtoupper((string) $this->karyawan_pranota?->tipe_bp_jamsostek));
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

    public function getKaryawanPranotaAttribute()
    {
        $snapshot = $this->snapshot_bpjs['karyawan'] ?? null;
        if ($snapshot !== null) {
            $class = $this->tipe_karyawan === KaryawanTidakTetap::class ? KaryawanTidakTetap::class : Karyawan::class;

            return (new $class)->forceFill($snapshot);
        }

        return $this->karyawan;
    }

    public function summaryBurden(string $key): float
    {
        if (isset($this->snapshot_bpjs['ringkasan'][$key])) {
            return (float) $this->snapshot_bpjs['ringkasan'][$key];
        }
        $jamsostekGroup = preg_replace('/[\s_-]+/', '', strtoupper((string) $this->karyawan_pranota?->group_bp_jamsostek));
        $jamsostekType = preg_replace('/[\s_-]+/', '', strtoupper((string) $this->karyawan_pranota?->tipe_bp_jamsostek));
        $useTotalPpu = $jamsostekGroup === 'PPU' && $jamsostekType === 'NONKARY';
        if ($key === 'kary_jht' && in_array($jamsostekType, ['NONKARY', 'NONKARYUMKMNONPP'], true)) {
            return 0;
        }
        $useNonEmployeeDebt = in_array($jamsostekGroup, ['CILANDAKHL', 'BPUSUPIRJKTPLUIT'], true)
            && $jamsostekType === 'NONKARYUMKMNONPP';
        $nonEmployee = fn ($type) => preg_replace('/[\s_-]+/', '', strtoupper((string) $type)) === 'NONKARY';
        $nonJkn = $nonEmployee($this->karyawan_pranota?->tipe_jkn);
        $jknGroup = preg_replace('/[\s_-]+/', '', strtoupper((string) $this->karyawan_pranota?->group_jkn));
        if ($key === 'non_kary' && ! $useNonEmployeeDebt) {
            return 0;
        }
        if (isset($this->tunjangan_manual[$key])) {
            return (float) $this->tunjangan_manual[$key];
        }
        $values = $this->rincian_bpjs ?? [];
        $sum = fn (array $fields) => array_sum(array_map(fn ($field) => (float) ($values[$field] ?? $this->getAttribute($field) ?? 0), $fields));
        $debt = $sum(['jht_hutang', 'jkk_hutang', 'noncrew_jht_hutang', 'jp_hutang']);
        $cost = $sum(['jht_biaya', 'noncrew_jht_biaya', 'jp_biaya']);
        $jknEmployerCost = $nonJkn && $jknGroup === 'JKNKIS'
            ? (float) $this->bpjs_kesehatan + (float) $this->bpjs_ketenagakerjaan : 0;
        $nonJamsostek = $nonEmployee($this->karyawan_pranota?->tipe_bp_jamsostek);
        $isUmkmNonPp = preg_replace('/[\s_-]+/', '', strtoupper((string) $this->karyawan_pranota?->tipe_bp_jamsostek)) === 'NONKARYUMKMNONPP';
        $useNoncrewCost = $isUmkmNonPp && in_array($jamsostekGroup, ['BPUSUPIR', 'BPUSUPIRJKTPLUIT', 'CILANDAKHL'], true);
        $noncrewCost = $this->rincian_bpjs !== null
            ? $sum(['noncrew_jht_biaya', 'noncrew_jkk_tunjangan', 'noncrew_jkm_tunjangan'])
            : $sum(['jht_biaya', 'jkk_tunjangan', 'jkm_tunjangan']);
        $umkmDebt = $this->rincian_bpjs !== null
            ? (float) ($values['noncrew_jht_hutang'] ?? 0)
            : (float) $this->jht_hutang;
        $ppuTotal = $sum(['jht_biaya', 'jht_hutang', 'jkk_tunjangan', 'jkm_tunjangan', 'jp_biaya', 'jp_hutang']);

        return match ($key) {
            'kary_jht' => $nonJamsostek ? 0 : $debt,
            'kary_jkn' => $nonJkn ? 0 : (float) $this->bpjs_ketenagakerjaan,
            'pt' => $jknEmployerCost + ($useTotalPpu ? $ppuTotal : ($useNoncrewCost ? $noncrewCost : $cost)),
            'non_kary' => $umkmDebt,
            default => 0,
        };
    }
}
