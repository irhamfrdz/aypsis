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
        'nomor_invoice',
        'nomor_referensi',
        'nama_kapal',
        'no_voyage',
        'no_bl',
        'jenis_biaya',
        'vendor_id',
        'nominal',
        'dp',
        'sisa_pembayaran',
        'penerima',
        'keterangan',
        'bukti',
        'ppn',
        'pph',
        'total_biaya',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
        'dp' => 'decimal:2',
        'sisa_pembayaran' => 'decimal:2',
        'ppn' => 'decimal:2',
        'pph' => 'decimal:2',
        'total_biaya' => 'decimal:2',
        'nama_kapal' => 'array',
        'no_voyage' => 'array',
        'no_bl' => 'array',
    ];

    // Accessors
    public function getFormattedNominalAttribute()
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    public function getJenisBiayaLabelAttribute()
    {
        // Use relationship to get nama from klasifikasi_biayas table
        return $this->klasifikasiBiaya->nama ?? $this->jenis_biaya;
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

    // Relationships
    public function barangDetails()
    {
        return $this->hasMany(BiayaKapalBarang::class, 'biaya_kapal_id');
    }

    public function klasifikasiBiaya()
    {
        return $this->belongsTo(KlasifikasiBiaya::class, 'jenis_biaya', 'kode');
    }

    public function vendor()
    {
        return $this->belongsTo(\App\Models\PricelistBiayaDokumen::class, 'vendor_id');
    }
}
