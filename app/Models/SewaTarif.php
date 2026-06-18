<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SewaTarif extends Model
{
    protected $table = 'sewa_tarifs';

    protected $primaryKey = 'id_tarif';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_tarif',
        'id_customer',
        'id_tipe',
        'id_ukuran',
        'tarif_bulanan',
        'tarif_harian',
        'tanggal_mulai_berlaku',
        'tanggal_akhir_berlaku',
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
}
