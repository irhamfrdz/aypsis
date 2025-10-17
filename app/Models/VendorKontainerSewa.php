<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorKontainerSewa extends Model
{
    protected $table = 'vendor_kontainer_sewas';
    
    protected $fillable = [
        'kode',
        'nama_vendor', 
        'catatan',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    // Status constants
    const STATUS_AKTIF = 'aktif';
    const STATUS_NON_AKTIF = 'nonaktif';

    public static function getStatusOptions()
    {
        return [
            self::STATUS_AKTIF => 'Aktif',
            self::STATUS_NON_AKTIF => 'Non-Aktif'
        ];
    }

    public function getStatusLabelAttribute()
    {
        return $this->status === self::STATUS_AKTIF ? 'Aktif' : 'Non-Aktif';
    }

    public function scopeAktif($query)
    {
        return $query->where('status', self::STATUS_AKTIF);
    }

    public function scopeNonAktif($query)
    {
        return $query->where('status', self::STATUS_NON_AKTIF);
    }
}
