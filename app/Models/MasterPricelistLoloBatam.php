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
        'size',
        'tarif',
        'status',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'tarif' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getFormattedTarifAttribute(): string
    {
        return 'Rp '.number_format((float) $this->tarif, 0, ',', '.');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
