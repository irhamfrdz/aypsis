<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class ProspekKapalKontainer extends Model
{
    use HasFactory, Auditable;

    protected $table = 'prospek_kapal_kontainers';

    protected $fillable = [
        'prospek_kapal_id',
        'tanda_terima_id',
        'tanda_terima_tanpa_sj_id',
        'nomor_kontainer',
        'ukuran_kontainer',
        'no_seal',
        'tanggal_loading',
        'loading_sequence',
        'status_loading',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_loading' => 'datetime',
        'loading_sequence' => 'integer',
    ];

    /**
     * Relationship dengan ProspekKapal
     */
    public function prospekKapal()
    {
        return $this->belongsTo(ProspekKapal::class, 'prospek_kapal_id');
    }

    /**
     * Relationship dengan TandaTerima
     */
    public function tandaTerima()
    {
        return $this->belongsTo(TandaTerima::class, 'tanda_terima_id');
    }

    /**
     * Relationship dengan TandaTerimaTanpaSuratJalan
     */
    public function tandaTerimaTanpaSuratJalan()
    {
        return $this->belongsTo(TandaTerimaTanpaSuratJalan::class, 'tanda_terima_tanpa_sj_id');
    }

    /**
     * Get the source document (tanda terima or tanda terima tanpa surat jalan)
     */
    public function getSourceDocumentAttribute()
    {
        if ($this->tanda_terima_id) {
            return $this->tandaTerima;
        } elseif ($this->tanda_terima_tanpa_sj_id) {
            return $this->tandaTerimaTanpaSuratJalan;
        }
        return null;
    }

    /**
     * Get document type
     */
    public function getDocumentTypeAttribute()
    {
        if ($this->tanda_terima_id) {
            return 'tanda_terima';
        } elseif ($this->tanda_terima_tanpa_sj_id) {
            return 'tanda_terima_tanpa_surat_jalan';
        }
        return null;
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status_loading) {
            'pending' => 'bg-gray-100 text-gray-800',
            'ready' => 'bg-blue-100 text-blue-800',
            'loading' => 'bg-yellow-100 text-yellow-800',
            'loaded' => 'bg-green-100 text-green-800',
            'problem' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status_loading) {
            'pending' => 'Menunggu',
            'ready' => 'Siap Loading',
            'loading' => 'Sedang Loading',
            'loaded' => 'Sudah Dimuat',
            'problem' => 'Bermasalah',
            default => 'Unknown'
        };
    }

    /**
     * Scope untuk filter berdasarkan status loading
     */
    public function scopeByStatusLoading($query, $status)
    {
        return $query->where('status_loading', $status);
    }

    /**
     * Scope untuk filter berdasarkan prospek kapal
     */
    public function scopeByProspekKapal($query, $prospekKapalId)
    {
        return $query->where('prospek_kapal_id', $prospekKapalId);
    }
}
