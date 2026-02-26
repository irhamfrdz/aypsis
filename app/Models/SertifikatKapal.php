<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class SertifikatKapal extends Model
{
    use SoftDeletes, Auditable;

    protected $table = 'sertifikat_kapals';

    protected $fillable = [
        'nama_sertifikat',
        'keterangan',
        'status'
    ];

    protected $casts = [
        'status' => 'string'
    ];

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
