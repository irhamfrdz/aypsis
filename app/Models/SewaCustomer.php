<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SewaCustomer extends Model
{
    protected $table = 'sewa_customers';

    protected $primaryKey = 'id_customer';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id_customer',
        'nama_customer',
    ];

    public function kontainers()
    {
        return $this->hasMany(SewaKontainer::class, 'id_customer', 'id_customer');
    }

    public function tarifs()
    {
        return $this->hasMany(SewaTarif::class, 'id_customer', 'id_customer');
    }

    public function transaksis()
    {
        return $this->hasMany(SewaTransaksi::class, 'id_customer', 'id_customer');
    }

    public function invoices()
    {
        return $this->hasMany(SewaInvoice::class, 'id_customer', 'id_customer');
    }
}
