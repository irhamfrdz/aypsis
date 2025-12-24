<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class BiayaKapal extends Model
{
    use SoftDeletes, Auditable;

    protected $table = 'biaya_kapals';

    protected $fillable = [
        'tanggal',
        'nama_kapal',
        'no_voyage',
        'jenis_biaya',
        'nominal',
        'keterangan',
        'bukti',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];

    // Accessors
    public function getFormattedNominalAttribute()
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    public function getJenisBiayaLabelAttribute()
    {
        $labels = [
            'bahan_bakar' => 'Bahan Bakar',
            'pelabuhan' => 'Pelabuhan',
            'perbaikan' => 'Perbaikan',
            'awak_kapal' => 'Awak Kapal',
            'asuransi' => 'Asuransi',
            'lainnya' => 'Lainnya',
        ];

        return $labels[$this->jenis_biaya] ?? $this->jenis_biaya;
    }

    public function getBuktiFotoAttribute()
    {
        if ($this->bukti && in_array(pathinfo($this->bukti, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png'])) {
            return asset('storage/' . $this->bukti);
        }
        return null;
    }

    public function getBuktiPdfAttribute()
    {
        if ($this->bukti && pathinfo($this->bukti, PATHINFO_EXTENSION) === 'pdf') {
            return asset('storage/' . $this->bukti);
        }
        return null;
    }

    // Scopes
    public function scopeBahanBakar($query)
    {
        return $query->where('jenis_biaya', 'bahan_bakar');
    }

    public function scopePelabuhan($query)
    {
        return $query->where('jenis_biaya', 'pelabuhan');
    }

    public function scopePerbaikan($query)
    {
        return $query->where('jenis_biaya', 'perbaikan');
    }

    public function scopeAwakKapal($query)
    {
        return $query->where('jenis_biaya', 'awak_kapal');
    }

    public function scopeAsuransi($query)
    {
        return $query->where('jenis_biaya', 'asuransi');
    }

    public function scopeLainnya($query)
    {
        return $query->where('jenis_biaya', 'lainnya');
    }

    public function scopeByNamaKapal($query, $namaKapal)
    {
        return $query->where('nama_kapal', $namaKapal);
    }

    public function scopeByPeriode($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }
}
