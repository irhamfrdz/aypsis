<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class SkSewa extends Model
{
    use Auditable;

    protected $table = 'sk_sewas';

    protected $fillable = [
        'no_kontainer',
        'vendor_id',
        'tanggal_sewa',
        'tanggal_kembali',
        'tarif_bulanan',
        'tarif_harian',
        'jenis_tarif',
        'status_sewa',
        'catatan',
        'non_ppn',
    ];

    protected $casts = [
        'tanggal_sewa' => 'date',
        'tanggal_kembali' => 'date',
        'tarif_bulanan' => 'integer',
        'tarif_harian' => 'integer',
        'non_ppn' => 'boolean',
    ];

    public function vendor()
    {
        return $this->belongsTo(VendorKontainerSewa::class, 'vendor_id');
    }

    public function kontainer()
    {
        return $this->belongsTo(SkKontainer::class, 'no_kontainer', 'no_kontainer');
    }

    public function tagihans()
    {
        return $this->hasMany(SkTagihanBulan::class, 'sewa_id');
    }
}
