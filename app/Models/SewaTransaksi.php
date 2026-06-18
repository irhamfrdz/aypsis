<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SewaTransaksi extends Model
{
    protected $table = 'sewa_transaksis';

    protected $primaryKey = 'id_sewa';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_sewa',
        'no_kontainer',
        'id_customer',
        'tanggal_sewa',
        'tanggal_kembali',
        'tarif_bulanan',
        'tarif_harian',
        'jenis_tarif',
        'status_sewa',
        'catatan',
    ];

    public function customer()
    {
        return $this->belongsTo(SewaCustomer::class, 'id_customer', 'id_customer');
    }

    public function kontainer()
    {
        return $this->belongsTo(SewaKontainer::class, 'no_kontainer', 'no_kontainer');
    }

    public function tagihans()
    {
        return $this->hasMany(SewaTagihan::class, 'id_sewa', 'id_sewa');
    }
}
