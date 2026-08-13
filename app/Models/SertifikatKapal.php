<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SertifikatKapal extends Model
{
    use Auditable, SoftDeletes;

    protected $table = 'sertifikat_kapals';

    protected $fillable = [
        'nama_sertifikat',
        'name_certificate',
        'nickname',
        'has_masa_berlaku',
        'status',
    ];

    protected $casts = [
        'has_masa_berlaku' => 'boolean',
        'status' => 'string',
    ];

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
