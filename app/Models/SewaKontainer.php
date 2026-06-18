<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SewaKontainer extends Model
{
    protected $table = 'sewa_kontainers';

    protected $primaryKey = 'no_kontainer';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'no_kontainer',
        'id_customer',
        'id_tipe',
        'id_ukuran',
        'status_aktif',
    ];

    protected $casts = [
        'status_aktif' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(SewaCustomer::class, 'id_customer', 'id_customer');
    }

    public function tipe()
    {
        return $this->belongsTo(SewaTipe::class, 'id_tipe', 'id_tipe');
    }

    public function ukuran()
    {
        return $this->belongsTo(SewaUkuran::class, 'id_ukuran', 'id_ukuran');
    }

    public function transaksis()
    {
        return $this->hasMany(SewaTransaksi::class, 'no_kontainer', 'no_kontainer');
    }
}
