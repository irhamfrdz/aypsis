<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class SkKontainer extends Model
{
    use Auditable;

    protected $table = 'sk_kontainers';

    protected $fillable = [
        'no_kontainer',
        'vendor_id',
        'tipe_id',
        'ukuran_id',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    public function vendor()
    {
        return $this->belongsTo(VendorKontainerSewa::class, 'vendor_id');
    }

    public function tipe()
    {
        return $this->belongsTo(SkTipeKontainer::class, 'tipe_id');
    }

    public function ukuran()
    {
        return $this->belongsTo(SkUkuranKontainer::class, 'ukuran_id');
    }

    public function sewas()
    {
        return $this->hasMany(SkSewa::class, 'no_kontainer', 'no_kontainer');
    }
}
