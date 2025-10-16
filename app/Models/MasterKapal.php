<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterKapal extends Model
{
    use SoftDeletes;

    protected $table = 'master_kapals';

    protected $fillable = [
        'kode',
        'kode_kapal',
        'nama_kapal',
        'catatan',
        'lokasi',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeNonaktif($query)
    {
        return $query->where('status', 'nonaktif');
    }
}
