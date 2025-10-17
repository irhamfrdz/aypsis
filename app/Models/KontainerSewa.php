<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontainerSewa extends Model
{
    protected $fillable = [
        'kode',
        'nama_vendor',
        'catatan',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    public function getStatusBadgeAttribute()
    {
        return $this->status === 'aktif' 
            ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>'
            : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Nonaktif</span>';
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeNonaktif($query)
    {
        return $query->where('status', 'nonaktif');
    }
}
