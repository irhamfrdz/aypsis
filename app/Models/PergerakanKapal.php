<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PergerakanKapal extends Model
{
    use HasFactory;

    protected $table = 'pergerakan_kapal';

    protected $fillable = [
        'nama_kapal',
        'kapten',
        'voyage',
        'transit',
        'tujuan_asal',
        'tujuan_tujuan',
        'tujuan_transit',
        'voyage_transit',
        'tanggal_sandar',
        'tanggal_labuh',
        'tanggal_berangkat',
        'status',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'transit' => 'boolean',
        'tanggal_sandar' => 'datetime',
        'tanggal_labuh' => 'datetime',
        'tanggal_berangkat' => 'datetime',
    ];

    /**
     * Get the status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'scheduled' => 'bg-blue-100 text-blue-800',
            'sailing' => 'bg-yellow-100 text-yellow-800',
            'arrived' => 'bg-green-100 text-green-800',
            'departed' => 'bg-purple-100 text-purple-800',
            'delayed' => 'bg-orange-100 text-orange-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get the status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'scheduled' => 'Terjadwal',
            'sailing' => 'Berlayar',
            'arrived' => 'Tiba',
            'departed' => 'Berangkat',
            'delayed' => 'Tertunda',
            'cancelled' => 'Dibatalkan',
            default => 'Unknown'
        };
    }

    /**
     * Get transit status label
     */
    public function getTransitLabelAttribute()
    {
        return $this->transit ? 'Ya' : 'Tidak';
    }

    /**
     * Get transit badge color
     */
    public function getTransitBadgeAttribute()
    {
        return $this->transit ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800';
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan nama kapal
     */
    public function scopeByKapal($query, $kapal)
    {
        return $query->where('nama_kapal', 'like', '%' . $kapal . '%');
    }

    /**
     * Scope untuk filter berdasarkan range tanggal
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_sandar', [$startDate, $endDate]);
    }

    /**
     * Scope untuk search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nama_kapal', 'like', '%' . $search . '%')
              ->orWhere('kapten', 'like', '%' . $search . '%')
              ->orWhere('voyage', 'like', '%' . $search . '%')
              ->orWhere('tujuan_asal', 'like', '%' . $search . '%')
              ->orWhere('tujuan_tujuan', 'like', '%' . $search . '%')
              ->orWhere('tujuan_transit', 'like', '%' . $search . '%');
        });
    }

    /**
     * Get duration of voyage in days
     */
    public function getVoyageDurationAttribute()
    {
        if ($this->tanggal_sandar && $this->tanggal_berangkat) {
            return $this->tanggal_berangkat->diffInDays($this->tanggal_sandar);
        }
        return null;
    }

    /**
     * Check if vessel is currently sailing
     */
    public function getIsSailingAttribute()
    {
        return $this->status === 'sailing';
    }

    /**
     * Check if vessel has arrived
     */
    public function getHasArrivedAttribute()
    {
        return in_array($this->status, ['arrived', 'departed']);
    }
}
