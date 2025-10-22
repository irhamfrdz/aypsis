<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class ProspekKapal extends Model
{
    use HasFactory, Auditable;

    protected $table = 'prospek_kapal';

    protected $fillable = [
        'pergerakan_kapal_id',
        'voyage',
        'nama_kapal',
        'tanggal_loading',
        'estimasi_departure',
        'jumlah_kontainer_terjadwal',
        'jumlah_kontainer_loaded',
        'status',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tanggal_loading' => 'datetime',
        'estimasi_departure' => 'datetime',
        'jumlah_kontainer_terjadwal' => 'integer',
        'jumlah_kontainer_loaded' => 'integer',
    ];

    /**
     * Relationship dengan PergerakanKapal
     */
    public function pergerakanKapal()
    {
        return $this->belongsTo(PergerakanKapal::class, 'pergerakan_kapal_id');
    }

    /**
     * Relationship dengan ProspekKapalKontainer
     */
    public function kontainers()
    {
        return $this->hasMany(ProspekKapalKontainer::class, 'prospek_kapal_id');
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'scheduled' => 'bg-blue-100 text-blue-800',
            'loading' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'draft' => 'Draft',
            'scheduled' => 'Terjadwal',
            'loading' => 'Sedang Loading',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => 'Unknown'
        };
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentageAttribute()
    {
        if ($this->jumlah_kontainer_terjadwal == 0) {
            return 0;
        }
        return round(($this->jumlah_kontainer_loaded / $this->jumlah_kontainer_terjadwal) * 100, 2);
    }

    /**
     * Check if loading is complete
     */
    public function getIsCompleteAttribute()
    {
        return $this->jumlah_kontainer_loaded >= $this->jumlah_kontainer_terjadwal;
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan voyage
     */
    public function scopeByVoyage($query, $voyage)
    {
        return $query->where('voyage', 'like', '%' . $voyage . '%');
    }

    /**
     * Scope untuk search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('voyage', 'like', '%' . $search . '%')
              ->orWhere('nama_kapal', 'like', '%' . $search . '%')
              ->orWhere('keterangan', 'like', '%' . $search . '%');
        });
    }
}
