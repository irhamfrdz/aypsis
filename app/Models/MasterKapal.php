<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterKapal extends Model
{
    use Auditable;
    use Auditable, SoftDeletes;

    protected $table = 'master_kapals';

    protected $fillable = [
        'kode',
        'kode_kapal',
        'nama_kapal',
        'nickname',
        'pelayaran',
        'kapasitas_kontainer_palka',
        'kapasitas_kontainer_deck',
        'gross_tonnage',
        'deadweight_tonnage',
        'length_overall',
        'catatan',
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

    // Accessors
    public function getTotalKapasitasKontainerAttribute()
    {
        return ($this->kapasitas_kontainer_palka ?? 0) + ($this->kapasitas_kontainer_deck ?? 0);
    }

    public function getFormattedKapasitasPalkaAttribute()
    {
        return $this->kapasitas_kontainer_palka ? number_format($this->kapasitas_kontainer_palka) : '-';
    }

    public function getFormattedKapasitasDeckAttribute()
    {
        return $this->kapasitas_kontainer_deck ? number_format($this->kapasitas_kontainer_deck) : '-';
    }

    public function getFormattedGrossTonnageAttribute()
    {
        return $this->gross_tonnage ? number_format($this->gross_tonnage, 2) : '-';
    }

    public function getFormattedDeadweightTonnageAttribute()
    {
        return $this->deadweight_tonnage ? number_format($this->deadweight_tonnage, 2) : '-';
    }

    public function getFormattedLengthOverallAttribute()
    {
        return $this->length_overall ? number_format($this->length_overall, 2) : '-';
    }

    public function getFormattedTotalKapasitasAttribute()
    {
        $total = $this->total_kapasitas_kontainer;

        return $total > 0 ? number_format($total) : '-';
    }

    public function dokumenKapalAlexindos()
    {
        return $this->hasMany(MasterDokumenKapalAlexindo::class, 'kapal_id');
    }
}
