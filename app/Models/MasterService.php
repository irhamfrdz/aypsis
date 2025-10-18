<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


use App\Traits\Auditable;
class MasterService extends Model
{
    use HasFactory, SoftDeletes;

    use Auditable;
    protected $fillable = [
        'kode_service',
        'nama_service',
        'deskripsi',
        'tarif',
        'status'
    ];

    protected $casts = [
        'tarif' => 'decimal:2'
    ];

    // Relationships
    public function gateIns()
    {
        return $this->hasMany(GateIn::class, 'service_id');
    }

    public function kontainers()
    {
        return $this->hasMany(Kontainer::class, 'service_id');
    }

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
    public function getFormattedNamaAttribute()
    {
        return $this->kode_service . ' - ' . $this->nama_service;
    }

    public function getFormattedTarifAttribute()
    {
        return $this->tarif ? 'Rp ' . number_format($this->tarif, 0, ',', '.') : '-';
    }
}
