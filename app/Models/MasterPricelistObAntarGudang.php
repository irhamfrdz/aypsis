<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterPricelistObAntarGudang extends Model
{
    use Auditable, HasFactory;

    protected $table = 'master_pricelist_ob_antar_gudang';

    protected $fillable = [
        'size_kontainer',
        'status_kontainer',
        'status_service',
        'gudang_tujuan_id',
        'biaya',
        'keterangan',
    ];

    protected $casts = [
        'biaya' => 'decimal:2',
    ];

    public static function getSizeKontainerOptions(): array
    {
        return ['20ft' => '20 ft', '40ft' => '40 ft'];
    }

    public static function getStatusKontainerOptions(): array
    {
        return ['full' => 'Full', 'empty' => 'Empty'];
    }

    public static function getStatusServiceOptions(): array
    {
        return ['service' => 'Service', 'non_service' => 'Bukan Service'];
    }

    public function getFormattedBiayaAttribute(): string
    {
        return 'Rp '.number_format($this->biaya, 0, ',', '.');
    }

    public function getSizeKontainerLabelAttribute(): string
    {
        return self::getSizeKontainerOptions()[$this->size_kontainer] ?? $this->size_kontainer;
    }

    public function getStatusKontainerLabelAttribute(): ?string
    {
        return self::getStatusKontainerOptions()[$this->status_kontainer] ?? $this->status_kontainer;
    }

    public function getStatusServiceLabelAttribute(): string
    {
        return self::getStatusServiceOptions()[$this->status_service] ?? $this->status_service;
    }

    public function gudangTujuan()
    {
        return $this->belongsTo(Gudang::class, 'gudang_tujuan_id');
    }
}
