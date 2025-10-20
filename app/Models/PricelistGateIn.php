<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class PricelistGateIn extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'pelabuhan',
        'kegiatan',
        'biaya',
        'gudang',
        'kontainer',
        'muatan',
        'tarif',
        'status'
    ];

    protected $casts = [
        'tarif' => 'decimal:2'
    ];

    // Relationships - removed as not needed for simplified structure
    // public function terminal()
    // {
    //     return $this->belongsTo(MasterTerminal::class, 'terminal_id');
    // }

    // public function kapal()
    // {
    //     return $this->belongsTo(MasterKapal::class, 'kapal_id');
    // }

    // public function service()
    // {
    //     return $this->belongsTo(MasterService::class, 'service_id');
    // }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeNonaktif($query)
    {
        return $query->where('status', 'nonaktif');
    }

    // Accessors
    public function getFormattedTarifAttribute()
    {
        return 'IDR ' . number_format((float) $this->tarif, 0, ',', '.');
    }
}
