<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class SkTarifSewa extends Model
{
    use Auditable;

    protected $table = 'sk_tarif_sewas';

    protected $fillable = [
        'vendor_id',
        'tipe_id',
        'ukuran_id',
        'tarif_bulanan',
        'tarif_harian',
        'tanggal_mulai_berlaku',
        'tanggal_akhir_berlaku',
        'status_aktif',
    ];

    protected $casts = [
        'tarif_bulanan' => 'integer',
        'tarif_harian' => 'integer',
        'tanggal_mulai_berlaku' => 'date',
        'tanggal_akhir_berlaku' => 'date',
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
}
