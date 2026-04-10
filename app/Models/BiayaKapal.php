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
        'adjustment',
        'dp',
        'sisa_pembayaran',
        'penerima',
        'nama_vendor',
        'nomor_rekening',
        'keterangan',
        'bukti',
        'ppn',
        'pph',
        'total_biaya',
        'pph_dokumen',
        'grand_total_dokumen',
        'status_pembayaran',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
        'adjustment' => 'decimal:2',
        'dp' => 'decimal:2',
        'sisa_pembayaran' => 'decimal:2',
        'ppn' => 'decimal:2',
        'pph' => 'decimal:2',
        'total_biaya' => 'decimal:2',
        'pph_dokumen' => 'decimal:2',
        'grand_total_dokumen' => 'decimal:2',
        'nama_kapal' => 'array',
        'no_voyage' => 'array',
        'no_bl' => 'array',
        'status_pembayaran' => 'string',
    ];

    // Accessors
    public function getFormattedNominalAttribute()
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    public function getDisplayNamaKapalAttribute()
    {
        if (is_array($this->nama_kapal)) {
            return implode(', ', $this->nama_kapal);
        }
        return (string) $this->nama_kapal;
    }

    public function getDisplayNoVoyageAttribute()
    {
        if (is_array($this->no_voyage)) {
            return implode(', ', $this->no_voyage);
        }
        return (string) $this->no_voyage;
    }

    public function getDisplayNoBlAttribute()
    {
        if (is_array($this->no_bl)) {
            return implode(', ', $this->no_bl);
        }
        return (string) $this->no_bl;
    }

    public function getStatusLabelAttribute()
    {
        $status = $this->status_pembayaran ?? 'pending';
        switch ($status) {
            case 'paid':
                return '<span class="px-2 py-1 text-xs font-semibold leading-tight text-green-700 bg-green-100 rounded-full">Lunas</span>';
            case 'cancelled':
                return '<span class="px-2 py-1 text-xs font-semibold leading-tight text-red-700 bg-red-100 rounded-full">Dibatalkan</span>';
            default:
                return '<span class="px-2 py-1 text-xs font-semibold leading-tight text-yellow-700 bg-yellow-100 rounded-full">Belum Lunas</span>';
        }
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
    public function scopePending($query)
    {
        return $query->where('status_pembayaran', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status_pembayaran', 'paid');
    }

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
    public function pembayarans()
    {
        return $this->belongsToMany(PembayaranBiayaKapal::class, 'pembayaran_biaya_kapal_items', 'biaya_kapal_id', 'pembayaran_biaya_kapal_id')
                    ->withPivot('nominal')
                    ->withTimestamps();
    }

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

    public function airDetails()
    {
        return $this->hasMany(BiayaKapalAir::class, 'biaya_kapal_id');
    }

    public function tkbmDetails()
    {
        return $this->hasMany(BiayaKapalTkbm::class, 'biaya_kapal_id');
    }

    public function operasionalDetails()
    {
        return $this->hasMany(BiayaKapalOperasional::class, 'biaya_kapal_id');
    }

    public function truckingDetails()
    {
        return $this->hasMany(BiayaKapalTrucking::class, 'biaya_kapal_id');
    }

    public function stuffingDetails()
    {
        return $this->hasMany(BiayaKapalStuffing::class, 'biaya_kapal_id');
    }

    public function perlengkapanDetails()
    {
        return $this->hasMany(BiayaKapalPerlengkapan::class, 'biaya_kapal_id');
    }

    public function labuhTambatDetails()
    {
        return $this->hasMany(BiayaKapalLabuhTambat::class, 'biaya_kapal_id');
    }

    public function oppOptDetails()
    {
        return $this->hasMany(BiayaKapalOppOpt::class, 'biaya_kapal_id');
    }

    public function thcDetails()
    {
        return $this->hasMany(BiayaKapalThc::class, 'biaya_kapal_id');
    }

    public function loloDetails()
    {
        return $this->hasMany(BiayaKapalLolo::class, 'biaya_kapal_id');
    }

    public function storageDetails()
    {
        return $this->hasMany(BiayaKapalStorage::class, 'biaya_kapal_id');
    }

    public function freightDetails()
    {
        return $this->hasMany(BiayaKapalFreight::class, 'biaya_kapal_id');
    }

    public function perijinanDetails()
    {
        return $this->hasMany(BiayaKapalPerijinan::class, 'biaya_kapal_id');
    }
}
