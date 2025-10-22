<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class MasterPelabuhan extends Model
{
    use HasFactory, Auditable;

    protected $table = 'master_pelabuhans';

    protected $fillable = [
        'nama_pelabuhan',
        'kota',
        'keterangan',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    // Scopes for filtering
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeNonaktif($query)
    {
        return $query->where('status', 'nonaktif');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('nama_pelabuhan', 'like', "%{$search}%")
              ->orWhere('kota', 'like', "%{$search}%")
              ->orWhere('keterangan', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'aktif' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>',
            'nonaktif' => '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Non-aktif</span>'
        ];

        return $badges[$this->status] ?? '';
    }

    // Relations
    public function pergerakanKapalAsal()
    {
        return $this->hasMany(PergerakanKapal::class, 'pelabuhan_asal', 'nama_pelabuhan');
    }

    public function pergerakanKapalTujuan()
    {
        return $this->hasMany(PergerakanKapal::class, 'pelabuhan_tujuan', 'nama_pelabuhan');
    }

    public function pergerakanKapalTransit()
    {
        return $this->hasMany(PergerakanKapal::class, 'pelabuhan_transit', 'nama_pelabuhan');
    }
}
