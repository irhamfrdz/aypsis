<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterPricelistLoloBatam extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    protected $table = 'master_pricelist_lolo_batams';

    protected $fillable = [
        'vendor',
        'nama_biaya',
        'kegiatan',
        'size',
        'tipe',
        'tarif',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tarif' => 'decimal:2',
    ];

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeSearch($query, $term)
    {
        if (empty($term)) {
            return $query;
        }

        $term = '%'.str_replace(' ', '%', $term).'%';

        return $query->where(function ($q) use ($term) {
            $q->where('nama_biaya', 'like', $term)
                ->orWhere('vendor', 'like', $term)
                ->orWhere('kegiatan', 'like', $term)
                ->orWhere('size', 'like', $term)
                ->orWhere('tipe', 'like', $term);
        });
    }

    public function getFormattedTarifAttribute(): string
    {
        return 'Rp '.number_format($this->tarif, 0, ',', '.');
    }
}
