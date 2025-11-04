<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TarifBatam extends Model
{
    use HasFactory;

    protected $table = 'tarif_batam';

    protected $fillable = [
        'chasis_ayp',
        '20ft_full',
        '20ft_empty',
        'antar_lokasi',
        '40ft_full',
        '40ft_empty',
        '40ft_antar_lokasi',
        'masa_berlaku',
        'keterangan',
        'status'
    ];

    protected $casts = [
        'chasis_ayp' => 'decimal:2',
        '20ft_full' => 'decimal:2',
        '20ft_empty' => 'decimal:2',
        'antar_lokasi' => 'decimal:2',
        '40ft_full' => 'decimal:2',
        '40ft_empty' => 'decimal:2',
        '40ft_antar_lokasi' => 'decimal:2',
        'masa_berlaku' => 'date',
    ];

    // Scope untuk tarif aktif
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // Scope untuk tarif yang masih berlaku
    public function scopeBerlaku($query)
    {
        return $query->where('masa_berlaku', '>=', now()->toDateString());
    }

    // Helper method untuk format currency
    public function getFormattedChasisAypAttribute()
    {
        return number_format($this->chasis_ayp, 0, ',', '.');
    }

    public function getFormatted20ftFullAttribute()
    {
        return number_format($this->attributes['20ft_full'], 0, ',', '.');
    }

    public function getFormatted20ftEmptyAttribute()
    {
        return number_format($this->attributes['20ft_empty'], 0, ',', '.');
    }

    public function getFormattedAntarLokasiAttribute()
    {
        return number_format($this->antar_lokasi, 0, ',', '.');
    }

    public function getFormatted40ftFullAttribute()
    {
        return number_format($this->attributes['40ft_full'], 0, ',', '.');
    }

    public function getFormatted40ftEmptyAttribute()
    {
        return number_format($this->attributes['40ft_empty'], 0, ',', '.');
    }

    public function getFormatted40ftAntarLokasiAttribute()
    {
        return number_format($this->attributes['40ft_antar_lokasi'], 0, ',', '.');
    }
}
